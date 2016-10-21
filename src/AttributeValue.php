<?php

namespace PHPSimpleHtmlPurify;

class AttributeValue
{
    protected $regex;

    protected $values;

    /**
     * @param string|array $values
     * @param bool|false $regex if true, $values will be used as regular expressions
     */
    public function __construct($values, $regex = false)
    {
        $this->values = is_array($values) ? $values : [$values];
        $this->regex  = $regex;
    }

    public function filter(\DOMAttr $attr, $black)
    {
        if ($this->regex) {
            if ($black) {
                foreach ($this->values as $value) {
                    $attr->value = preg_replace($value, '', $attr->value);
                }
            } else {
                $validValues = '';
                foreach ($this->values as $value) {
                    $matches = [];
                    if (preg_match($value, $attr->value, $matches) === 1) {
                        $validValues .= "{$matches[0]} ";
                    }
                }
                $attr->value = trim($validValues);
            }
        } else {
            if ($black) {
                foreach ($this->values as $value) {
                    $attr->value = str_replace($value, '', $attr->value);
                }
            } else {
                $validValues = '';
                foreach ($this->values as $value) {
                    if (strpos($attr->value, $value) !== false) {
                        $validValues .= "{$value} ";
                    }
                }
                $attr->value = trim($validValues);
            }
        }
    }
}
