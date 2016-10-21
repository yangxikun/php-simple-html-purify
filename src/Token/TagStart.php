<?php

namespace PHPSimpleHtmlPurify\Token;

class TagStart
{
    protected $name;

    protected $attributes;

    protected $content;

    public function __construct(\DOMElement $node)
    {
        $this->name = $node->tagName;
        $this->content = "<{$this->name}";
        $this->attributes = [];
        foreach ($node->attributes as $attr) {
            $this->content .= " {$attr->name}=\"{$attr->value}\" ";
            $this->attributes[$attr->name] = $attr->value;
        }
        $this->content .= '>';
        if (in_array($this->name, ['script', 'style'])) {
            $this->content .= $node->nodeValue;
        }
    }

    public function name()
    {
        return $this->name;
    }

    public function attributes()
    {
        return $this->attributes;
    }

    public function __toString()
    {
        return $this->content;
    }
}
