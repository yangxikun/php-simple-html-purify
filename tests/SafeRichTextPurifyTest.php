<?php

use PHPSimpleHtmlPurify\Attribute;
use PHPSimpleHtmlPurify\AttributeValue;
use PHPSimpleHtmlPurify\Purifier;
use PHPSimpleHtmlPurify\Tag;

class SafeRichTextPurifyTest extends PHPUnit_Framework_TestCase
{
    public function testPurifyRichText()
    {
        $dirtyHtml = <<<EOD
<div onclick="alert('xss')">
<style type="text/css">h1 {color:red}</style>
<iframe src="/i/eg_landscape.jpg"></iframe>
<form action="form_action.asp" method="get">
<input type="text" name="fname" />
<input type="submit" value="Submit" />
</form>
<script type="text/javascript">
document.write("Hello World!")
</script>
<p>Hello World</p>
<div style="position:absolute;background-color:green;width:170px;height:80px;margin:20px;padding-top:20px;color:#ffffff;font-weight:bold;font-size:18px;float:left;text-align:center; font-family:'微软雅黑';" onmouseover="this.innerHTML='谢谢'" onmouseout="this.innerHTML='把鼠标移到上面'">把鼠标移到上面</div>
</div>
EOD;
        $purifier = new Purifier();
        /*
        $purifier->tagBlackList(new Tag(['html', 'body', 'head', 'meta', 'object', 'param', 'script', 'noscript',
            'style', 'iframe', 'form', 'input', 'select', 'button', 'textarea', 'fieldset', 'frame', 'frameset',
            'noframes', 'applet', 'link']));
        $purifier->attrBlackList(new Attribute('/on\w+/', true));
        */
        $tagWhiteList = explode(',', 'a, abbr, acronym, address, area, article, aside, b, bdi, big,
            blockquote, br, caption, center, cite, code, col, colgroup, data, datalist, dd, del, details, dfn,
            dir, div, dl, dt, em, figcaption, figure, font, h1, h2, h3, h4, h5, h6, hr,
            i, img, ins, kbd, keygen, label, legend, li, main, map, mark, menu, menuitem, meter, nav, ol,
            output, p, pre, progress, q, rp, rt, ruby, s, samp, section, small, span, strike,
            strong, sub, summary, sup, table, tbody, td, tfoot, th, thead, time, tr, tt, u, ul, var, wbr'
        );
        array_walk($tagWhiteList, function (&$item) {
            $item = trim($item);
        });
        $purifier->tagWhiteList(new Tag($tagWhiteList));
        $attrWhiteList = explode(',', 'abbr, accept, accept-charset, accesskey, action, align,
            alt, autocomplete, autosave, axis, bgcolor, border, cellpadding, cellspacing, challenge, char, charoff,
            charset, checked, cite, clear, color, cols, colspan, compact, contenteditable, coords, datetime, dir,
            disabled, draggable, dropzone, enctype, for, frame, headers, height, high, href, hreflang, hspace, ismap,
            keytype, label, lang, list, longdesc, low, max, maxlength, media, method, min, multiple, name, nohref,
            noshade, novalidate, nowrap, open, optimum, pattern, placeholder, prompt, pubdate, radiogroup, readonly,
            rel, required, rev, reversed, rows, rowspan, rules, scope, selected, shape, size, span, spellcheck, src,
            start, step, style, summary, tabindex, target, title, type, usemap, valign, value, vspace, width, wrap'
        );
        array_walk($attrWhiteList, function (&$item) {
            $item = trim($item);
        });
        $purifier->attrWhiteList(new Attribute($attrWhiteList));
        $purifier->attrBlackList(new Attribute('style', false, null, new AttributeValue('/position *: *\w+;?/i', true)));
        $cleanHtml = <<<EOD
<div>
<p>Hello World</p>
<div style="background-color:green;width:170px;height:80px;margin:20px;padding-top:20px;color:#ffffff;font-weight:bold;font-size:18px;float:left;text-align:center; font-family:'微软雅黑';" >把鼠标移到上面</div>
</div>
EOD;

        $this->assertXmlStringEqualsXmlString($cleanHtml, $purifier->purify($dirtyHtml));
    }
}
