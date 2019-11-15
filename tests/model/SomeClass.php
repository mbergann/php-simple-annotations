<?php

namespace frostbane\DocBlockReader\test\model;

use frostbane\DocBlockReader\Reader;
use PHPUnit\Framework\TestCase;

/**
 * @number       1
 * @string "123"
 * @string2      abc
 * @string7      akane kitamoto
 * @array ["a", "b"]
 * @object {"x": "y"}
 * @nested {"x": {"y": "z"}}
 * @nestedArray {"x": {"y": ["z", "p"]}}
 *
 * @trueVar
 * @null-var     null
 * @string4 "null"
 *
 * @booleanTrue  true
 * @string3      tRuE
 * @booleanFalse false
 * @string5 "true"
 * @string6 "false"
 * @booleanNull  null
 *
 */
class SomeClass extends TestCase
{
    /**
     * @number       1
     * @string "123"
     * @string2      abc
     * @array ["a", "b"]
     * @string7      akane kitamoto
     * @object {"x": "y"}
     * @nested {"x": {"y": "z"}}
     * @nestedArray {"x": {"y": ["z", "p"]}}
     *
     * @trueVar
     * @null-var     null
     * @string4 "null"
     *
     * @booleanTrue  true
     * @string3      tRuE
     * @booleanFalse false
     * @string5 "true"
     * @string6 "false"
     * @booleanNull  null
     *
     */
    public $myVar = "test";

    /**
     * @x 1
     * @y yes!
     */
    private $myVar2;

    /**
     * my constant
     *
     * @get some value
     */
    const MY_CONST = "shashee";

    /**
     * @number       1
     * @string "123"
     * @string2      abc
     * @array ["a", "b"]
     * @string7      akane kitamoto
     * @object {"x": "y"}
     * @nested {"x": {"y": "z"}}
     * @nestedArray {"x": {"y": ["z", "p"]}}
     *
     * @trueVar
     * @null-var     null
     * @string4 "null"
     *
     * @booleanTrue  true
     * @string3      tRuE
     * @booleanFalse false
     * @string5 "true"
     * @string6 "false"
     * @booleanNull  null
     *
     */
    const MY_OTHER_CONST = "akane";

    /**
     * @Lalala ["somejsonarray", "2"]
     * @Lalala ["anotherjsonarray", "3"]
     */
    public $issue2;

    /**
     * @number       1
     * @string "123"
     * @string2      abc
     * @string7      akane kitamoto
     * @array ["a", "b"]
     * @object {"x": "y"}
     * @nested {"x": {"y": "z"}}
     * @nestedArray {"x": {"y": ["z", "p"]}}
     *
     * @trueVar
     * @null-var     null
     * @string4 "null"
     *
     * @booleanTrue  true
     * @string3      tRuE
     * @booleanFalse false
     * @string5 "true"
     * @string6 "false"
     * @booleanNull  null
     *
     */
    private function parserFixture()
    {
    }

    /**
     * @number       1
     * @string "123"
     * @string2      abc
     * @string7      akane kitamoto
     * @array ["a", "b"]
     * @object {"x": "y"}
     * @nested {"x": {"y": "z"}}
     * @nestedArray {"x": {"y": ["z", "p"]}}
     *
     * @trueVar
     * @null-var     null
     * @string4 "null"
     *
     * @booleanTrue  true
     * @string3      tRuE
     * @booleanFalse false
     * @string5 "true"
     * @string6 "false"
     * @booleanNull  null
     *
     */
    private static function staticFixture()
    {
    }

    private function parserEmptyFixture()
    {
    }

    /**
     * @var x
     * @var2 1024
     *
     * @param string x
     * @param integer y
     * @param array z
     */
    private function parserMultiFixture()
    {
    }

    /**
     * @allowedRequest ["ajax", "post"]
     * @postParam integer orderId
     * @postParam array productIds
     * @postParam string newValue
     */
    private function fixtureThree()
    {

    }

    /**
     * @get @post
     * @ajax
     * @postParam x
     * @postParam y
     * @postParam z
     */
    private function fixtureFour()
    {
    }

    /**
     * @trueVar1
     * @trueVar2
     */
    private function fixtureFive()
    {
    }

    /**
     * @param string var1
     * @param integer var2
     */
    private function fixtureVariableDeclarations()
    {
    }

    /**
     * @param false
     */
    private function fixtureBadVariableDeclarationsOne()
    {
    }

    /**
     * @param true
     */
    private function fixtureBadVariableDeclarationsTwo()
    {
    }
}
