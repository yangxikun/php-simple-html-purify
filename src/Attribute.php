<?php

namespace PHPSimpleHtmlPurify;

class Attribute
{
    protected $names;

    protected $tag;

    protected $regex;

    protected $value;

    /**
     * @param string|array $names attribute name
     * @param bool|false $regex if true, $names will be used as regular expressions
     * @param Tag|null $tag belong to which tag
     * @param AttributeValue|null $value
     */
    public function __construct($names, $regex = false, Tag $tag = null, AttributeValue $value = null)
    {
        $this->names  = is_array($names) ? $names : [$names];
        $this->tag   = is_null($tag) ? new Tag('*') : $tag;
        $this->regex = $regex;
        $this->value = $value;
    }

    /**
     * @param $attrName
     * @return bool if $attrName match Attribute::$names, return true
     */
    public function match($attrName)
    {
        if ($this->regex) {
            $matches = [];
            foreach ($this->names as $name) {
                if (preg_match($name, $attrName, $matches) === 1) {
                    return true;
                }
            }
            return false;
        }

        if (in_array('*', $this->names) || in_array($attrName, $this->names)) {
            return true;
        }

        return false;
    }

    /**
     * @param \DOMElement $node
     * @param bool $black Is it in blacklist rule
     */
    public function filter(\DOMElement $node, $black)
    {
        if ($this->tag->match($node->tagName)) {
            $removeAttrs = [];
            foreach ($node->attributes as $attr) {
                if ($this->match($attr->name)) {
                    if ($this->value) {
                        $this->value->filter($attr, $black);
                    } else {
                        if ($black && !$this->value) {
                            $removeAttrs[] = $attr->name;
                        }
                    }
                } elseif (!$black && !$this->value) {
                    $removeAttrs[] = $attr->name;
                }
            }
            foreach ($removeAttrs as $attr) {
                $node->removeAttribute($attr);
            }
        }
    }
}
