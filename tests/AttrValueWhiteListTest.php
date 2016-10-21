<?php

use PHPSimpleHtmlPurify\AttributeValue;
use PHPSimpleHtmlPurify\Purifier;
use PHPSimpleHtmlPurify\Attribute;

class AttrValueWhiteListTest extends PHPUnit_Framework_TestCase
{
    public function testNormalStringAttrValueParameter()
    {
        $dirtyHtml = '<div class="test" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrWhiteList(new Attribute('class', false, null, new AttributeValue('notest')));
        $this->assertXmlStringEqualsXmlString('<div class="" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }

    public function testNormalStringAttrValueArrayParameter()
    {
        $dirtyHtml = '<div class="test" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrWhiteList(new Attribute('class', false, null, new AttributeValue(['notest', 'test'])));
        $this->assertXmlStringEqualsXmlString('<div class="test" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexAttrValueParameter()
    {
        $dirtyHtml = '<div class="test-hello" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrWhiteList(new Attribute('class', false, null, new AttributeValue('/notest-\w+/', true)));
        $this->assertXmlStringEqualsXmlString('<div class="" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexAttrValueArrayParameter()
    {
        $dirtyHtml = '<div class="test-world" style="align-content: center"></div>';
        $purifier = new Purifier();
        $purifier->attrWhiteList(new Attribute('class', false, null, new AttributeValue(['/notest-\d+/', '/test-\w+/'], true)));
        $this->assertXmlStringEqualsXmlString('<div class="test-world" style="align-content: center"></div>', $purifier->purify($dirtyHtml));
    }
}
