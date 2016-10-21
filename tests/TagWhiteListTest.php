<?php

use PHPSimpleHtmlPurify\Purifier;
use PHPSimpleHtmlPurify\Tag;

class TagWhiteListTest extends PHPUnit_Framework_TestCase
{
    public function testNormalStringTagParameter()
    {
        $dirtyHtml = '<div class="test"><script>alert("xss");</script></div>';
        $purifier = new Purifier();
        $purifier->tagWhiteList(new Tag('div'));
        $this->assertXmlStringEqualsXmlString('<div class="test"></div>', $purifier->purify($dirtyHtml));
    }

    public function testNormalStringTagArrayParameter()
    {
        $dirtyHtml = '<div><style type="text/css">h1 {color:red}</style><p>Hello</p><div class="test"><script>alert("xss");</script></div></div>';
        $purifier = new Purifier();
        $purifier->tagWhiteList(new Tag(['div','p']));
        $this->assertXmlStringEqualsXmlString('<div><p>Hello</p><div class="test"></div></div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexTagParameter()
    {
        $dirtyHtml = '<div class="test"><AF-kk>Hello</AF-kk></div>';
        $purifier = new Purifier();
        $purifier->tagWhiteList(new Tag('/af-\w+/', true));
        $this->assertXmlStringEqualsXmlString('<af-kk>Hello</af-kk>', $purifier->purify($dirtyHtml));
    }

    public function testRegexTagArrayParameter()
    {
        $dirtyHtml = '<fcc><div><div class="test"><AF-kk>Hello</AF-kk></div><v:fb>World</v:fb><v:fa>NoNo</v:fa></div></fcc>';
        $purifier = new Purifier();
        $purifier->tagWhiteList(new Tag(['/af-\w+/','/f\w+/'], true));
        $this->assertXmlStringEqualsXmlString('<fcc><af-kk>Hello</af-kk><fb>World</fb><fa>NoNo</fa></fcc>', $purifier->purify($dirtyHtml));
    }
}
