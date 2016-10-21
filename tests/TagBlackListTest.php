<?php

use PHPSimpleHtmlPurify\Purifier;
use PHPSimpleHtmlPurify\Tag;

class TagBlackListTest extends PHPUnit_Framework_TestCase
{
    public function testNormalStringTagParameter()
    {
        $dirtyHtml = '<div class="test"><script>alert("xss");</script></div>';
        $purifier = new Purifier();
        $purifier->tagBlackList(new Tag('script'));
        $this->assertXmlStringEqualsXmlString('<div class="test"></div>', $purifier->purify($dirtyHtml));
    }

    public function testNormalStringTagArrayParameter()
    {
        $dirtyHtml = '<style type="text/css">h1 {color:red}</style><div class="test"><script>alert("xss");</script></div>';
        $purifier = new Purifier();
        $purifier->tagBlackList(new Tag(['script','style']));
        $this->assertXmlStringEqualsXmlString('<div class="test"></div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexTagParameter()
    {
        $dirtyHtml = '<div class="test"><AF-kk>Hello</AF-kk></div>';
        $purifier = new Purifier();
        $purifier->tagBlackList(new Tag('/af-\w+/', true));
        $this->assertXmlStringEqualsXmlString('<div class="test">Hello</div>', $purifier->purify($dirtyHtml));
    }

    public function testRegexTagArrayParameter()
    {
        $dirtyHtml = '<div><div class="test"><AF-kk>Hello</AF-kk></div><v:fb>World</v:fb><v:fa>NoNo</v:fa></div>';
        $purifier = new Purifier();
        $purifier->tagBlackList(new Tag(['/af-\w+/','/f\w+/'], true));
        $this->assertXmlStringEqualsXmlString('<div><div class="test">Hello</div>WorldNoNo</div>', $purifier->purify($dirtyHtml));
    }
}
