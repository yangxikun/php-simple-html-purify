<?php

namespace PHPSimpleHtmlPurify\Token;

class Text
{
    protected $content;

    public function __construct(\DOMNode $node)
    {
        $this->content = $node->wholeText;
    }

    public function __toString()
    {
        return $this->content;
    }
}
