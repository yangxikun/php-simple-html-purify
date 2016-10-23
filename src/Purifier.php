<?php

namespace PHPSimpleHtmlPurify;

use PHPSimpleHtmlPurify\Token\Comment;
use PHPSimpleHtmlPurify\Token\TagEmpty;
use PHPSimpleHtmlPurify\Token\TagEnd;
use PHPSimpleHtmlPurify\Token\TagStart;
use PHPSimpleHtmlPurify\Token\Text;
use SplQueue;

class Purifier
{
    protected $selfClosingTags = [
        'area',
        'base',
        'br',
        'col',
        'command',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
    ];

    protected $tagBlackList = [];
    protected $tagWhiteList = [];

    protected $attrBlackList = [];
    protected $attrWhiteList = [];

    protected $attrValueBlackList = [];
    protected $attrValueWhiteList = [];

    /**
     * Add Tag to BlackList
     *
     * @param Tag $tag
     */
    public function tagBlackList(Tag $tag)
    {
        $this->tagBlackList[] = $tag;
    }

    /**
     * Add tag to WhiteList
     *
     * @param Tag $tag
     */
    public function tagWhiteList(Tag $tag)
    {
        $this->tagWhiteList[] = $tag;
    }

    /**
     * Add attr to BlackList
     *
     * @param Attribute $attr
     */
    public function attrBlackList(Attribute $attr)
    {
        $this->attrBlackList[] = $attr;
    }

    /**
     * Add attr to WhiteList
     *
     * @param Attribute $attr
     */
    public function attrWhiteList(Attribute $attr)
    {
        $this->attrWhiteList[] = $attr;
    }

    /**
     * Add attrValue to BlackList
     *
     * @param AttributeValue $attrValue
     */
    public function attrValueBlackList(AttributeValue $attrValue)
    {
        $this->attrValueBlackList[] = $attrValue;
    }

    /**
     * Add attrValue to WhiteList
     *
     * @param AttributeValue $attrValue
     */
    public function attrValueWhiteList(AttributeValue $attrValue)
    {
        $this->attrValueWhiteList[] = $attrValue;
    }

    protected function tokenizeAndClean($dirtyHtml, $encoding)
    {
        $dom = new \DOMDocument();
        $dom->encoding = $encoding;
        set_error_handler([$this, 'muteErrorHandler']);
        $dom->loadHTML($dirtyHtml);
        restore_error_handler();
        $body = $dom->getElementsByTagName('html')->item(0)->getElementsByTagName('body')->item(0);
        $level = 0;
        $queue = new SplQueue();
        $queue->enqueue($body);
        $nodes  = [$level => $queue];
        $tokens = [];
        $closingNodes = [];
        do {
            while (!$nodes[$level]->isEmpty()) {
                $node = $nodes[$level]->dequeue();
                switch ($node->nodeType) {
                    case XML_TEXT_NODE:
                        $tokens[] = new Text($node);
                        continue;
                        break;
                    case XML_COMMENT_NODE:
                        $tokens[] = new Comment($node);
                        continue;
                    case XML_ELEMENT_NODE:
                        $keep = true;
                        foreach ($this->tagBlackList as $tag) {
                            if ($tag->match($node->tagName)) {
                                $keep = false;
                                break;
                            }
                        }
                        if (!$keep) {
                            break;
                        }
                        $keep = false;
                        foreach ($this->tagWhiteList as $tag) {
                            if ($tag->match($node->tagName)) {
                                $keep = true;
                                break;
                            }
                        }
                        if ($keep || empty($this->tagWhiteList)) {
                            $removeAttrs = [];
                            foreach ($node->attributes as $attribute) {
                                $keep = true;
                                foreach ($this->attrBlackList as $attr) {
                                    if ($attr->tagMatch($node->tagName) && $attr->match($attribute->name)) {
                                        $keep = false;
                                        break;
                                    }
                                }
                                if (!$keep) {
                                    $removeAttrs[] = $attribute->name;
                                    continue;
                                }
                                $keep = false;
                                $tagNotMatchCount = 0;
                                foreach ($this->attrWhiteList as $attr) {
                                    if ($attr->tagMatch($node->tagName)) {
                                        if ($attr->match($attribute->name)) {
                                            $keep = true;
                                            break;
                                        }
                                    } else {
                                        $tagNotMatchCount++;
                                    }
                                }
                                if (!$keep && count($this->attrWhiteList) != $tagNotMatchCount) {
                                    $removeAttrs[] = $attribute->name;
                                    continue;
                                }

                                $keep = true;
                                foreach ($this->attrValueBlackList as $attrValue) {
                                    if ($attrValue->attrMatch($attribute) && $attrValue->match($attribute->value) !== false) {
                                        $attrValue->remove($attribute);
                                        $keep = false;
                                        break;
                                    }
                                }
                                if ($keep && !empty($this->attrValueWhiteList)) {
                                    $validValues = [];
                                    $attrNotMatch = 0;
                                    foreach ($this->attrValueWhiteList as $attrValue) {
                                        if ($attrValue->attrMatch($attribute)) {
                                            $matchValue = $attrValue->match($attribute->value);
                                            if ($matchValue !== false) {
                                                $validValues[] = $matchValue;
                                            }
                                        } else {
                                            $attrNotMatch++;
                                        }
                                    }
                                    if (!empty($validValues)) {
                                        $attribute->value = implode(' ', $validValues);
                                    } elseif (count($this->attrValueWhiteList) != $attrNotMatch) {
                                        $attribute->value = '';
                                    }
                                }
                            }
                            foreach ($removeAttrs as $attrName) {
                                $node->removeAttribute($attrName);
                            }

                            if (in_array($node->tagName, $this->selfClosingTags)) {
                                $token = new TagEmpty($node);
                            } else {
                                $token = new TagStart($node);
                                $closingNodes[$level][] = $token;
                            }
                            $tokens[] = $token;
                        }
                        break;
                    default:
                        continue;
                }
                if ($node->hasChildNodes()) {
                    $level++;
                    $queue = new SplQueue();
                    foreach ($node->childNodes as $childNode) {
                        $queue->enqueue($childNode);
                    }
                    $nodes[$level] = $queue;
                }
            }
            if (isset($closingNodes[$level])) {
                while ($token = array_pop($closingNodes[$level])) {
                    $tokens[] = new TagEnd($token);
                }
            }
            $level--;
            if ($level >= 0 && isset($closingNodes[$level])) {
                while ($token = array_pop($closingNodes[$level])) {
                    $tokens[] = new TagEnd($token);
                }
            }
        } while ($level >= 0);

        return $tokens;
    }

    protected function genCleanHtml(array $tokens)
    {
        if (empty($tokens)) {
            return '';
        }
        if (($tokens[0] instanceof TagStart || $tokens[0] instanceof TagEmpty)
            && $tokens[0]->name() == 'body') {
            array_shift($tokens);
            array_pop($tokens);
        }

        $cleanHtml = '';
        foreach ($tokens as $token) {
            $cleanHtml .= $token;
        }

        return $cleanHtml;
    }

    public function muteErrorHandler($errno, $errstr) {}

    protected function wrapHtml($html, $encoding)
    {
        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=' . $encoding . '"/></head><body>'
            . $html . '</body></html>';

        return $html;
    }

    /**
     * purify dirtyHtml
     *
     * @param $dirtyHtml
     * @param string $encoding $dirtyHtml encoding
     * @return string cleanHtml
     */
    public function purify($dirtyHtml, $encoding = 'UTF-8')
    {
        $tokens = $this->tokenizeAndClean($this->wrapHtml($dirtyHtml, $encoding), $encoding);

        return $this->genCleanHtml($tokens);
    }
}
