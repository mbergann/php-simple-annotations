<?php

namespace frostbane\DocBlockReader\test;

use PHPUnit\Framework\TestCase;
use frostbane\DocBlockReader\Reader;
use frostbane\DocBlockReader\test\model\SomeClass;

class ReaderTest extends TestCase
{
    public function testPropertyParsing()
    {
        $reader = new Reader(SomeClass::class, 'myVar', 'property');
        $this->commonTest($reader);
    }

    public function testPropertyParsing2()
    {
        $reader = new Reader(SomeClass::class, 'myVar2', 'property');
        $x      = $reader->getParameter("x");
        $y      = $reader->getParameter("y");
        $this->assertSame(1, $x);
        $this->assertSame("yes!", $y);
    }

    /**
     * Issue:
     * @see https://github.com/jan-swiecki/php-simple-annotations/issues/2
     *      Thanks to @KrekkieD (https://github.com/KrekkieD) for
     *      reporting this issue!
     */
    public function testIssue2Problem()
    {
        $reader = new Reader(SomeClass::class, 'issue2', 'property');
        $Lalala = $reader->getParameters()["Lalala"];

        $this->assertSame(array("somejsonarray", "2", "anotherjsonarray", "3"), $Lalala);
    }

    public function testParserOne()
    {
        $reader = new Reader(SomeClass::class, 'parserFixture');
        $this->commonTest($reader);
    }

    /**
     * @param Reader $reader
     */
    public function commonTest($reader)
    {
        $parameters = $reader->getParameters();

        $this->assertNotEmpty($parameters);

        $this->assertArrayHasKey('number', $parameters);
        $this->assertArrayHasKey('string', $parameters);
        $this->assertArrayHasKey('string2', $parameters);
        $this->assertArrayHasKey('string3', $parameters);
        $this->assertArrayHasKey('string4', $parameters);
        $this->assertArrayHasKey('string5', $parameters);
        $this->assertArrayHasKey('string6', $parameters);
        $this->assertArrayHasKey('string7', $parameters);
        $this->assertArrayHasKey('array', $parameters);
        $this->assertArrayHasKey('object', $parameters);
        $this->assertArrayHasKey('nested', $parameters);
        $this->assertArrayHasKey('nestedArray', $parameters);
        $this->assertArrayHasKey('trueVar', $parameters);
        $this->assertArrayHasKey('null-var', $parameters);
        $this->assertArrayHasKey('booleanTrue', $parameters);
        $this->assertArrayHasKey('booleanFalse', $parameters);
        $this->assertArrayHasKey('booleanNull', $parameters);
        $this->assertArrayNotHasKey('non_existent_key', $parameters);

        $this->assertSame(1, $parameters['number']);
        $this->assertSame("123", $parameters['string']);
        $this->assertSame("abc", $parameters['string2']);
        $this->assertSame(array("a", "b"), $parameters['array']);
        $this->assertSame(array("x" => "y"), $parameters['object']);
        $this->assertSame(array("x" => array("y" => "z")), $parameters['nested']);
        $this->assertSame(array("x" => array("y" => array("z", "p"))), $parameters['nestedArray']);
        $this->assertSame(true, $parameters['trueVar']);
        $this->assertSame(null, $parameters['null-var']);

        $this->assertSame(true, $parameters['booleanTrue']);
        $this->assertSame("tRuE", $parameters['string3']);
        $this->assertSame("true", $parameters['string5']);
        $this->assertSame("false", $parameters['string6']);
        $this->assertSame(false, $parameters['booleanFalse']);
        $this->assertSame(null, $parameters['booleanNull']);
        $this->assertSame("null", $parameters['string4']);
        $this->assertSame("akane kitamoto", $parameters['string7']);
        $this->assertNull($reader->getParameter("non_existent_key"));
    }

    public function testParserOneFromClass()
    {
        $reader     = new Reader(SomeClass::class);
        $parameters = $reader->getParameters();

        $this->assertNotEmpty($parameters);

        $this->assertArrayHasKey('number', $parameters);
        $this->assertArrayHasKey('string', $parameters);
        $this->assertArrayHasKey('array', $parameters);
        $this->assertArrayHasKey('object', $parameters);
        $this->assertArrayHasKey('nested', $parameters);
        $this->assertArrayHasKey('nestedArray', $parameters);
        $this->assertArrayHasKey('trueVar', $parameters);
        $this->assertArrayHasKey('null-var', $parameters);
        $this->assertArrayHasKey('booleanTrue', $parameters);
        $this->assertArrayHasKey('booleanFalse', $parameters);
        $this->assertArrayHasKey('booleanNull', $parameters);
        $this->assertArrayNotHasKey('non_existent_key', $parameters);

        $this->assertSame(1, $parameters['number']);
        $this->assertSame("123", $parameters['string']);
        $this->assertSame("abc", $parameters['string2']);
        $this->assertSame(array("a", "b"), $parameters['array']);
        $this->assertSame(array("x" => "y"), $parameters['object']);
        $this->assertSame(array("x" => array("y" => "z")), $parameters['nested']);
        $this->assertSame(array("x" => array("y" => array("z", "p"))), $parameters['nestedArray']);
        $this->assertSame(true, $parameters['trueVar']);
        $this->assertSame(null, $parameters['null-var']);

        $this->assertSame(true, $parameters['booleanTrue']);
        $this->assertSame("tRuE", $parameters['string3']);
        $this->assertSame(false, $parameters['booleanFalse']);
        $this->assertSame(null, $parameters['booleanNull']);
    }

    public function testParserTwo()
    {
        $reader = new Reader(SomeClass::class, 'parserFixture');

        $this->assertSame(1, $reader->getParameter('number'));
        $this->assertSame("123", $reader->getParameter('string'));
        $this->assertSame(array("x" => array("y" => array("z", "p"))),
                          $reader->getParameter('nestedArray'));

        $this->assertSame(null, $reader->getParameter('nullVar'));
        $this->assertSame(null, $reader->getParameter('null-var'));
        $this->assertSame(null, $reader->getParameter('non-existent'));
    }

    public function testParserEmpty()
    {
        $reader     = new Reader(SomeClass::class, 'parserEmptyFixture');
        $parameters = $reader->getParameters();
        $this->assertSame(array(), $parameters);
    }

    public function testParserMulti()
    {
        $reader     = new Reader(SomeClass::class, 'parserMultiFixture');
        $parameters = $reader->getParameters();

        $this->assertNotEmpty($parameters);
        $this->assertArrayHasKey('param', $parameters);
        $this->assertArrayHasKey('var', $parameters);

        $this->assertSame("x", $parameters["var"]);
        $this->assertSame(1024, $parameters["var2"]);

        $this->assertSame(
            array("string x", "integer y", "array z"),
            $parameters["param"]);

    }

    public function testParserThree()
    {
        $reader = new Reader(SomeClass::class, 'fixtureThree');
        // $allowedRequest = $reader->getParameter("allowedRequest");

        $postParam = $reader->getParameter("postParam");

        $this->assertNotEmpty($postParam);
    }

    public function testParserFour()
    {
        $reader = new Reader(SomeClass::class, 'fixtureFour');

        $this->assertSame(true, $reader->getParameter('get'));
        $this->assertSame(true, $reader->getParameter('post'));
        $this->assertSame(true, $reader->getParameter('ajax'));
        $this->assertSame(array("x", "y", "z"), $reader->getParameter('postParam'));
    }

    public function testParserFourBis()
    {
        $reader = new Reader(SomeClass::class, 'fixtureFour');

        $parameters = $reader->getParameters();

        $this->assertArrayHasKey('get', $parameters);
        $this->assertArrayHasKey('post', $parameters);
        $this->assertArrayHasKey('ajax', $parameters);
        $this->assertArrayHasKey('postParam', $parameters);

        $this->assertSame(true, $parameters['get']);
        $this->assertSame(true, $parameters['post']);
        $this->assertSame(true, $parameters['ajax']);
        $this->assertSame(array("x", "y", "z"), $parameters['postParam']);

    }

    public function testFive()
    {
        $reader1 = new Reader(SomeClass::class, 'fixtureFive');
        $reader2 = new Reader(SomeClass::class, 'fixtureFive');

        $parameters1 = $reader1->getParameters();

        $trueVar1 = $parameters1['trueVar1'];

        $this->assertSame(true, $trueVar1);
        $this->assertSame(true, $reader2->getParameter("trueVar2"));

    }

    public function testVariableDeclarations()
    {
        $reader       = new Reader(SomeClass::class, 'fixtureVariableDeclarations');
        $declarations = $reader->getVariableDeclarations("param");
        $this->assertNotEmpty($declarations);

        $this->assertSame(array(
                              array("type" => "string", "name" => "var1"),
                              array("type" => "integer", "name" => "var2"),
                          ), $declarations);
    }

    /**
     * @dataProvider badVariableDataProvider
     */
    public function testBadVariableDeclarations($methodName)
    {
        $this->expectException(\InvalidArgumentException::class);

        $reader = new Reader(SomeClass::class, $methodName);

        $reader->getVariableDeclarations("param");
    }

    public function badVariableDataProvider()
    {
        return array(
            array('fixtureBadVariableDeclarationsOne'),
            array('fixtureBadVariableDeclarationsTwo'),
        );
    }

    public function testConstantParser()
    {
        $reader = new Reader(SomeClass::class, "MY_CONST", "constant");

        $parameters = $reader->getParameters();

        $this->assertSame("some value", $parameters["get"]);
    }

    public function testConstantParserCommon()
    {
        $reader = new Reader(SomeClass::class, "MY_OTHER_CONST", "constant");

        $this->commonTest($reader);
    }
}
