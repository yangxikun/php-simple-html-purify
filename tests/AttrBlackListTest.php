<?php

use PHPSimpleHtmlPurify\Purifier;
use PHPSimpleHtmlPurify\Tag;
use PHPSimpleHtmlPurify\Attribute;

class AttrBlackListTest extends PHPUnit_Framework_TestCase
{
    public function testNormalStringAttrParameter()
    {
        $dirtyHtml = '<div class="test" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrBlackList(new Attribute('style'));
        $this->assertXmlStringEqualsXmlString('<div class="test"></div>', $purifier->purify($dirtyHtml));
    }

    public function testNormalStringAttrArrayParameter()
    {
        $dirtyHtml = '<div class="test" style="align-content: center" onclick="alert(\'xss\')"></div>';
        $purifier = new Purifier();
        $purifier->attrBlackList(new Attribute(['style', 'onclick']));
        $this->assertXmlStringEqualsXmlString('<div class="test"></div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexAttrParameter()
    {
        $dirtyHtml = '<div data-id=123 class="test" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrBlackList(new Attribute('/data-\w+/', true));
        $this->assertXmlStringEqualsXmlString('<div class="test" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexAttrArrayParameter()
    {
        $dirtyHtml = '<div data-id=123 data-content="Hello" class="test" style="align-content: center" onclick="alert(\'xss\')"></div>';
        $purifier = new Purifier();
        $purifier->attrBlackList(new Attribute(['/data-\w+/', '/on\w+/'], true));
        $this->assertXmlStringEqualsXmlString('<div class="test" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }

    public function testWithTag()
    {
        $dirtyHtml = '<div data-id=123 data-content="Hello" class="test" style="align-content: center" onclick="alert(\'xss\')"><p data-content="world"></p></div>';
        $purifier = new Purifier();
        $purifier->attrBlackList(new Attribute(['/data-\w+/', '/on\w+/'], true, new Tag('div')));
        $this->assertXmlStringEqualsXmlString('<div class="test" style="align-content: center"><p data-content="world"></p></div>', $purifier->purify($dirtyHtml));
        $this->assertXmlStringNotEqualsXmlString('<div class="test" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }
}
