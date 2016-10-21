<?php

namespace PHPSimpleHtmlPurify;

class Tag
{
    protected $names;

    protected $regex;

    /**
     * @param string|array $names tag name
     * @param bool|false $regex if true, $names will be used as regular expressions
     */
    public function __construct($names, $regex = false)
    {
        $this->names = is_array($names) ? $names : [$names];
        $this->regex = $regex;
    }

    /**
     * @param $tagName
     * @return bool if $tagName match Tag::$names, return true
     */
    public function match($tagName)
    {
        if ($this->regex) {
            $matches = [];
            foreach ($this->names as $name) {
                if (preg_match($name, $tagName, $matches) === 1) {
                    return true;
                }
            }
            return false;
        }

        if (in_array('*', $this->names) || in_array($tagName, $this->names)) {
            return true;
        }

        return false;
    }
}
