<?php

namespace PHPSimpleHtmlPurify\Token;

class Text
{
    protected $content;

    public function __construct(\DOMNode $node)
    {
        $this->content = htmlspecialchars($node->wholeText);
    }

    public function __toString()
    {
        return $this->content;
    }
}
