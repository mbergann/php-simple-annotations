# PHP Simple Annotations

## Installation

Get [composer](http://getcomposer.org/) and learn to use it.

Library is on [packagist](https://packagist.org/packages/frostbane/php-simple-annotations).

If you refuse to use composer then instead of `include_once "vendor/autoload.php"` use `include_once "src/DocBlockReader/Reader.php"`.

## Test

You need [PHPUnit](https://github.com/sebastianbergmann/phpunit/). After you get it run:

    > git clone https://github.com/frostbane/php-simple-annotations
    > cd php-simple-annotations
    > composer install
    > phpunit

## Introduction

This library gives you the ability to extract and auto-parse DocBlock comment blocks.

Example:
```php
    class TestClass {
      /**
       * @x 1
       * @y yes!
       */
      private $myVar;
    }

    $reader = new \frostbane\DocBlockReader\Reader('TestClass', 'myVar', 'property');
    $x = $reader->getParameter("x"); // 1 (with number type)
    $y = $reader->getParameter("y"); // "yes!" (with string type)
 ```

So as you can see to do this you need to construct `Reader` object and target it at something. Then you extract data.

You can point at classes, class methods and class properties.

* Targeting class: `$reader = new \frostbane\DocBlockReader\Reader(String $className)`
* Targeting method or property: `$reader = new \frostbane\DocBlockReader\Reader(String $className, String $name [, String $type = 'method'])`

 This will initialize DocBlock Reader on method `$className::$name` or property `$className::$name`.

 To choose method use only two arguments or provide third argument as `method` string value. To get property value put `property` string value in third argument.

To extract parsed properties you have two methods:

* `$reader->getParameter(String $key)`

 Returns DocBlock value of parameter `$key`. E.g.

 ```php
 <?php
 class MyClass
 {
     /**
      * @awesomeVariable "I am a string"
      */
     public function fn()
     {

     }
 }
 ```

 then

 ```php
 $reader = new \frostbane\DocBlockReader\Reader('MyClass', 'fn');
 $reader->getParameter("awesomeVariable")
 ```

 will return string `I am a string` (without quotes).

* `$reader->getParameters()`

 returns array of all parameters (see examples below).

## API

* Constructor `$reader = new \frostbane\DocBlockReader\Reader(String $className [, String $name [, String $type = 'method'] ])`

  Creates `Reader` pointing at class, class method or class property - based on provided arguments (see Introduction).

* `$reader->getParameter(String $key)`

 Returns value of parameter `$key` extracted from DocBlock.

* `$reader->getParameters()`

 returns array of all parameters (see examples below).

* `$reader->getVariableDeclarations()` - See last example below.


## Examples

Examples based on ReaderTest.php.

Note: DocBlock Reader converts type of values basing on the context (see below).

### Type conversion example

```php
<?php

include_once "../vendor/autoload.php";

class MyClass
{
    /**
     * @float_0-0         0.0
     * @float_1-5         1.5
     * @int_1             1
     * @int_0             0
     * @string_2-3 "2.3"
     * @string_1   "1"
     * @string_0   "0"
     * @string_0-0 "0.0"
     * @string_123 "123"
     * @string_4-5 "4.5"
     *
     * @string_abc        abc
     * @string_def  "def"
     *
     * @array1 ["a", "b"]
     * @obj1 {"x": "y"}
     * @obj2 {"x": {"y": "z"}}
     * @obj_array1 {"x": {"y": ["z", "p"]}}
     *
     * @empty1
     * @null1             null
     * @string_null "null"
     *
     * @bool_true         true
     * @bool_false        false
     *
     * @string_tRuE       tRuE
     * @string_fAlSe      fAlSe
     * @string_true  "true"
     * @string_false "false"
     *
     */
    private function MyMethod()
    {
    }
}

$reader = new \frostbane\DocBlockReader\Reader("MyClass", "MyMethod");

var_dump($reader->getParameters());
```

will print



<pre class='xdebug-var-dump' dir='ltr'>
<b>array</b> <i>(size=25)</i>
  'float_0-0' <font color='#888a85'>=&gt;</font> <small>float</small> <font color='#f57900'>0</font>
  'float_1-5' <font color='#888a85'>=&gt;</font> <small>float</small> <font color='#f57900'>1.5</font>
  'int_1' <font color='#888a85'>=&gt;</font> <small>int</small> <font color='#4e9a06'>1</font>
  'int_0' <font color='#888a85'>=&gt;</font> <small>int</small> <font color='#4e9a06'>0</font>
  'string_2-3' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'2.3'</font> <i>(length=3)</i>
  'string_1' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'1'</font> <i>(length=1)</i>
  'string_0' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'0'</font> <i>(length=1)</i>
  'string_0-0' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'0.0'</font> <i>(length=3)</i>
  'string_123' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'123'</font> <i>(length=3)</i>
  'string_4-5' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'4.5'</font> <i>(length=3)</i>
  'string_abc' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'abc'</font> <i>(length=3)</i>
  'string_def' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'def'</font> <i>(length=3)</i>
  'array1' <font color='#888a85'>=&gt;</font>
    <b>array</b> <i>(size=2)</i>
      0 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'a'</font> <i>(length=1)</i>
      1 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'b'</font> <i>(length=1)</i>
  'obj1' <font color='#888a85'>=&gt;</font>
    <b>array</b> <i>(size=1)</i>
      'x' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'y'</font> <i>(length=1)</i>
  'obj2' <font color='#888a85'>=&gt;</font>
    <b>array</b> <i>(size=1)</i>
      'x' <font color='#888a85'>=&gt;</font>
        <b>array</b> <i>(size=1)</i>
          'y' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'z'</font> <i>(length=1)</i>
  'obj_array1' <font color='#888a85'>=&gt;</font>
    <b>array</b> <i>(size=1)</i>
      'x' <font color='#888a85'>=&gt;</font>
        <b>array</b> <i>(size=1)</i>
          'y' <font color='#888a85'>=&gt;</font>
            <b>array</b> <i>(size=2)</i>
              ...
  'empty1' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'null1' <font color='#888a85'>=&gt;</font> <font color='#3465a4'>null</font>
  'string_null' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'null'</font> <i>(length=4)</i>
  'bool_true' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'bool_false' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>false</font>
  'string_tRuE' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'tRuE'</font> <i>(length=4)</i>
  'string_fAlSe' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'fAlSe'</font> <i>(length=5)</i>
  'string_true' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'true'</font> <i>(length=4)</i>
  'string_false' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'false'</font> <i>(length=5)</i>
</pre>

### Multi value example

```php
<?php

include_once "vendor/autoload.php";

class MyClass
{
	/**
	 * @var x
	 * @var2 1024
	 * @param string x
	 * @param integer y
	 * @param array z
	 */
	private function MyMethod()
	{
	}
};

$reader = new \frostbane\DocBlockReader\Reader("MyClass", "MyMethod");

var_dump($reader->getParameters());
```

will print


<pre class='xdebug-var-dump' dir='ltr'>
<b>array</b> <i>(size=3)</i>
  'var' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'x'</font> <i>(length=1)</i>
  'var2' <font color='#888a85'>=&gt;</font> <small>int</small> <font color='#4e9a06'>1024</font>
  'param' <font color='#888a85'>=&gt;</font>
    <b>array</b> <i>(size=3)</i>
      0 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'string x'</font> <i>(length=8)</i>
      1 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'integer y'</font> <i>(length=9)</i>
      2 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'array z'</font> <i>(length=7)</i>
</pre>

### Variables on the same line

```php
<?php

include_once "vendor/autoload.php";

class MyClass
{
	/**
	 * @get @post
	 * @ajax
	 * @postParam x @postParam y
	 * @postParam z
	 */
	private function MyMethod()
	{
	}
};

$reader = new \frostbane\DocBlockReader\Reader("MyClass", "MyMethod");

var_dump($reader->getParameters());
```

will print

<pre class='xdebug-var-dump' dir='ltr'>
<b>array</b> <i>(size=4)</i>
  'get' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'post' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'ajax' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'postParam' <font color='#888a85'>=&gt;</font>
    <b>array</b> <i>(size=3)</i>
      0 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'x'</font> <i>(length=1)</i>
      1 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'y'</font> <i>(length=1)</i>
      2 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'z'</font> <i>(length=1)</i>
</pre>

### Variable declarations functionality example

I found below functionality useful for filtering `$_GET`/`$_POST` data in CodeIgniter. Hopefully I will soon release my CodeIgniter's modification.

```php
<?php

include_once "vendor/autoload.php";

class MyClass
{
	/**
	 * @param string var1
	 * @param integer var2
	 */
	private function MyMethod()
	{
	}
};

$reader = new \frostbane\DocBlockReader\Reader("MyClass", "MyMethod");

var_dump($reader->getVariableDeclarations("param"));
```

will print

<pre class='xdebug-var-dump' dir='ltr'>
<b>array</b> <i>(size=2)</i>
  0 <font color='#888a85'>=&gt;</font>
    <b>array</b> <i>(size=2)</i>
      'type' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'string'</font> <i>(length=6)</i>
      'name' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'var1'</font> <i>(length=4)</i>
  1 <font color='#888a85'>=&gt;</font>
    <b>array</b> <i>(size=2)</i>
      'type' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'integer'</font> <i>(length=7)</i>
      'name' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'var2'</font> <i>(length=4)</i>
</pre>
