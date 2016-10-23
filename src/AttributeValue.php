<?php

namespace PHPSimpleHtmlPurify;

class AttributeValue
{
    protected $regex;

    protected $values;

    protected $attr;

    /**
     * @param string|array $values
     * @param bool|false $regex if true, $values will be used as regular expressions
     * @param Attribute|null belong to which attribute
     */
    public function __construct($values, $regex = false, Attribute $attr = null)
    {
        $this->values = is_array($values) ? $values : [$values];
        $this->regex  = $regex;
        $this->attr   = $attr;
    }

    public function attrMatch(\DOMAttr $attr)
    {
        if (!$this->attr) {
            return true;
        }

        if (!$this->attr->tagMatch($attr->ownerElement->tagName) || !$this->attr->match($attr->name)) {
            return false;
        }

        return true;
    }

    public function match($attrValue)
    {
        if ($this->regex) {
            $matches = [];
            foreach ($this->values as $valuePattern) {
                if (preg_match($valuePattern, $attrValue, $matches) === 1) {
                    return $matches[0];
                }
            }
        } else {
            foreach ($this->values as $value) {
                if (strpos($attrValue, $value) !== false) {
                    return $value;
                }
            }
        }

        return false;
    }

    public function remove(\DOMAttr $attr)
    {
        if ($this->regex) {
            foreach ($this->values as $value) {
                $attr->value = preg_replace($value, '', $attr->value);
            }
        } else {
            foreach ($this->values as $value) {
                $attr->value = str_replace($value, '', $attr->value);
            }
        }
    }
}
