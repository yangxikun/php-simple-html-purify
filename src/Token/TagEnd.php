<?php

namespace PHPSimpleHtmlPurify\Token;

class TagEnd
{
    protected $name;

    protected $content;

    public function __construct(TagStart $token)
    {
        $this->name = $token->name();
        $this->content = "</{$this->name}>";
    }

    public function __toString()
    {
        return $this->content;
    }
}
