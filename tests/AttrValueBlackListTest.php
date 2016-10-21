<?php

use PHPSimpleHtmlPurify\AttributeValue;
use PHPSimpleHtmlPurify\Purifier;
use PHPSimpleHtmlPurify\Attribute;

class AttrValueBlackListTest extends PHPUnit_Framework_TestCase
{
    public function testNormalStringAttrValueParameter()
    {
        $dirtyHtml = '<div class="test" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrBlackList(new Attribute('class', false, null, new AttributeValue('test')));
        $this->assertXmlStringEqualsXmlString('<div class="" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }

    public function testNormalStringAttrValueArrayParameter()
    {
        $dirtyHtml = '<div class="notest" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrBlackList(new Attribute('class', false, null, new AttributeValue(['notest', 'test'])));
        $this->assertXmlStringEqualsXmlString('<div class="" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexAttrValueParameter()
    {
        $dirtyHtml = '<div class="test-hello" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrBlackList(new Attribute('class', false, null, new AttributeValue('/notest-\w+/', true)));
        $this->assertXmlStringEqualsXmlString('<div class="test-hello" style="align-content: center"></div>', $purifier->purify($dirtyHtml));

        $dirtyHtml = '<div class="test-world" style="align-content: center"><p class="test"></p></div>';
        $purifier = new Purifier();
        $purifier->attrBlackList(new Attribute('class', false, null, new AttributeValue('/^test$/', true)));
        $this->assertXmlStringEqualsXmlString('<div class="test-world" style="align-content: center"><p class=""></p></div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexAttrValueArrayParameter()
    {
        $dirtyHtml = '<div class="test-world" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrBlackList(new Attribute('class', false, null, new AttributeValue(['/notest-\d+/', '/test-\w+/'], true)));
        $this->assertXmlStringEqualsXmlString('<div class="" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }
}
