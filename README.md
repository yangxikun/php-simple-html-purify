# php-simple-html-purify
A php simple html purify. This library doesn't apply any HTML specification. You should configure all rules by yourself.

### How it works

```
                 +-----------+
                 | dirtyHtml |
                 +-----+-----+
                       |
  +------------------> | <-------------------+
  |                    |                     |
  |      +-------------v---------------+     |
  |      |  apply Tag BlackList rules  |     |
  |      |                             |     |
  |      |                             |     |
No|      |                             |     |
  |      |                             |     |
  |      |  apply Tag WhiteList rules  |     |
  |      |                             |     |
  |      +-------------+---------------+     |
  |                    |                     |
  |                    |                     |
  |                    +                     |
  +------------+if tag was keep              |
                       +                     |
                       | Yes                 |
       +---------------v-----------------+   |
       | apply Attribute BlackList rules |   |
       |                                 |   |
       |                                 |   |
       |                                 |   |
       | apply Attribute WhiteList rules |   |
       |                                 |   |
       +---------------+-----------------+   |
                       |                     |
                       |                     |
                       +                     |
  +-----------+if attribute was keep         |
  |                    +                     |
  |                    | Yes                 |
  |   +----------------v-------------------+ |
  |   |apply AttributeValue BalckList rules| |
No|   |                                    | |
  |   |                                    | |
  |   |                                    | |
  |   |apply AttributeValue WhiteList rules| |
  |   +----------------+-------------------+ |
  |                    |                     |
  |                    |                     |
  |                    v                     |
  |          +---------+---------+           |
  +---------^+ collect valid tag |           |
             +---------+---------+           |
                       |                     | No
                       |                     |
                       +                     |
          if all tags has been purify +------+
                       +
                       | Yes
                       |
             +---------v----------+
             | generate cleanHtml |
             +--------------------+
```


### example

Filter tag:

```php
<?php
use PHPSimpleHtmlPurify\Purifier;
use PHPSimpleHtmlPurify\Tag;

require './vendor/autoload.php';

$dirtyHtml = '<div><script>alert("xss");</script><p>Hello Wrold</p></div>';
$htmlPurifier = new Purifier();
$htmlPurifier->tagBlackList(new Tag('script'));//add script to tag blacklist rules
echo $htmlPurifier->purify($dirtyHtml);//output: <div><p>Hello Wrold</p></div>

$htmlPurifier = new Purifier();
$htmlPurifier->tagWhiteList(new Tag(['p', 'div']));//add p, div to tag whitelist rules
echo $htmlPurifier->purify($dirtyHtml);//output: <div><p>Hello Wrold</p></div>

//tag name also support regular expression, see source directory tests/*Test.php
```

Filter attribute:

```php
$dirtyHtml = '<div style="color: #080808" class="data-content"><p>Hello World</p></div>';
$htmlPurifier = new Purifier();
$htmlPurifier->attrBlackList(new Attribute('class'));//add class to attribute blacklist, apply to all tag
echo $htmlPurifier->purify($dirtyHtml);//output: <div style="color: #080808" ><p>Hello World</p></div>

$dirtyHtml = '<div style="color: #080808" class="data-content"><p style="color: #101010">Hello World</p></div>';
$htmlPurifier = new Purifier();
$htmlPurifier->attrWhiteList(new Attribute('style', false, new Tag('div')));//add style to attribute whitelist, apply to div tag
echo $htmlPurifier->purify($dirtyHtml);//output: <div style="color: #080808" ><p style="color: #101010" >Hello World</p></div>

//attribute name also support regular expression, see source directory tests/*Test.php
```

Filter attribute value:

```php
$dirtyHtml = '<div style="color: #080808;position: absolute" class="data-content"><p style="color: #101010">Hello World</p></div>';
$htmlPurifier = new Purifier();
$htmlPurifier->attrValueBlackList(new AttributeValue('/position *: *absolute;?/', true, new Attribute('style')));//add style to attributeValue blacklist, apply to all tag
echo $htmlPurifier->purify($dirtyHtml);//output: <div style="color: #080808;"  class="data-content" ><p style="color: #101010" >Hello World</p></div>

$dirtyHtml = '<div style="color: #080808;position: absolute" class="data-content"><p style="color: #101010;font-size: 12px">Hello World</p></div>';
$htmlPurifier = new Purifier();
$htmlPurifier->attrValueWhiteList(new AttributeValue(['/color: *#\d+;?/', '/font-size: *\d+px;?/'], true, new Attribute('style', false, new Tag('div'))));//add style to attribute whitelist, apply to div tag
echo $htmlPurifier->purify($dirtyHtml);//output: <div style="color: #080808;"  class="data-content" ><p style="color: #101010; font-size: 12px" >Hello World</p></div>
```

For more use case, see source directory tests/*Test.php.
