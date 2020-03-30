<?php

namespace Coderey\DocBlockReader\test\model;

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
function someMethod()
{

}
