<?php

use PHPSimpleHtmlPurify\Purifier;
use PHPSimpleHtmlPurify\Tag;
use PHPSimpleHtmlPurify\Attribute;

class AttrWhiteListTest extends PHPUnit_Framework_TestCase
{
    public function testNormalStringAttrParameter()
    {
        $dirtyHtml = '<div class="test" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrWhiteList(new Attribute('class'));
        $this->assertXmlStringEqualsXmlString('<div class="test"></div>', $purifier->purify($dirtyHtml));
    }

    public function testNormalStringAttrArrayParameter()
    {
        $dirtyHtml = '<div class="test" field="value1" style="align-content: center" onclick="alert(\'xss\')"></div>';
        $purifier = new Purifier();
        $purifier->attrWhiteList(new Attribute(['class', 'field']));
        $this->assertXmlStringEqualsXmlString('<div class="test" field="value1"></div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexAttrParameter()
    {
        $dirtyHtml = '<div data-id=123 class="test" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrWhiteList(new Attribute('/data-\w+/', true));
        $this->assertXmlStringEqualsXmlString('<div data-id="123"></div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexAttrArrayParameter()
    {
        $dirtyHtml = '<div data-id=123 data-content="Hello" class="test" style="align-content: center" onclick="alert(\'xss\')"></div>';
        $purifier = new Purifier();
        $purifier->attrWhiteList(new Attribute(['/data-\w+/', '/style/', '/class/'], true));
        $this->assertXmlStringEqualsXmlString('<div data-id="123" data-content="Hello" class="test" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }

    public function testWithTag()
    {
        $dirtyHtml = '<div data-id=123 data-content="Hello" class="test" style="align-content: center" onclick="alert(\'xss\')"><p data-content="world"></p></div>';
        $purifier = new Purifier();
        $purifier->attrWhiteList(new Attribute(['class', 'style'], false, new Tag('div')));
        $this->assertXmlStringEqualsXmlString('<div class="test" style="align-content: center"><p data-content="world"></p></div>', $purifier->purify($dirtyHtml));
        $this->assertXmlStringNotEqualsXmlString('<div class="test" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }
}

