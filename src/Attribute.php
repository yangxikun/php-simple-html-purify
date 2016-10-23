<?php

namespace PHPSimpleHtmlPurify;

class Attribute
{
    protected $names;

    protected $tag;

    protected $regex;

    /**
     * @param string|array $names attribute name
     * @param bool|false $regex if true, $names will be used as regular expressions
     * @param Tag|null $tag belong to which tag
     */
    public function __construct($names, $regex = false, Tag $tag = null)
    {
        $this->names  = is_array($names) ? $names : [$names];
        $this->tag   = is_null($tag) ? new Tag('*') : $tag;
        $this->regex = $regex;
    }

    public function tagMatch($tagName)
    {
        if ($this->tag && !$this->tag->match($tagName)) {
            return false;
        }

        return true;
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
}
