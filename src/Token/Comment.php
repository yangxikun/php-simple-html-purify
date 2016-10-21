<?php

namespace PHPSimpleHtmlPurify\Token;

class Comment
{
    protected $content;

    public function __construct(\DOMComment $node)
    {
        $this->content = "<!-- {$node->data} -->";
    }

    public function __toString()
    {
        return $this->content;
    }
}
