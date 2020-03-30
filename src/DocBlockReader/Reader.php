<?php

namespace Coderey\DocBlockReader;

class Reader
{
    /** @var string */
    private $rawDocBlock;
    /** @var array */
    private $parameters;
    /** @var bool */
    private $parsedAll = false;

    private const keyPattern = '[A-z0-9\_\-]+';
    private const endPattern = "[ ]*(?:@|\r\n|\n)";

    public const TYPE_METHOD   = "* method";
    public const TYPE_PROPERTY = "* property";
    public const TYPE_CONSTANT = "* constant";
    public const TYPE_FUNCTION = "* function";

    /**
     * @param $arguments
     *
     * @return \ReflectionClass|\ReflectionClassConstant|\ReflectionFunction|\ReflectionMethod|\ReflectionProperty
     * @throws \ReflectionException
     */
    private function create($arguments)
    {
        @list($class, $method, $type) = $arguments;

        $count = count($arguments);

        // get reflection from class or class/method
        // (depends on constructor arguments)
        if ($count === 0) {
            throw new \DomainException("No zero argument constructor allowed");
        } else if ($count === 1) {
            if (is_string($class) && function_exists($class)) {
                return new \ReflectionFunction($class);
            } else {
                return new \ReflectionClass($class);
            }
        } else {
            if ($count === 2) {
                if ($method === self::TYPE_FUNCTION) {
                    return new \ReflectionFunction($class);
                }

                $type = self::TYPE_METHOD;
            }

            if ($type === self::TYPE_METHOD) {
                return new \ReflectionMethod($class, $method);
            } else if ($type === self::TYPE_PROPERTY) {
                return new \ReflectionProperty($class, $method);
            } else if ($type === self::TYPE_CONSTANT) {
                return new \ReflectionClassConstant($class, $method);
            }
        }
    }

    /**
     * Reader constructor.
     *
     * $class could be one of the following
     *
     * - callable
     * - class name
     * - class instance
     *
     * throws \ReflectionException
     * (@ throws annotation is implicitly defined)
     *
     * @param mixed       $class
     * @param null|string $method
     * @param null|string $type
     *
     */
    public function __construct($class, ?string $method = null, ?string $type = null)
    {
        if (is_array($class)) {
            $method = $class[1];
            $class  = $class[0];
            $type   = self::TYPE_METHOD;
        }

        $arguments = array($class);

        $method !== null and $arguments[] = $method;
        $type !== null and $arguments[] = $type;

        $reflection = $this->create($arguments);

        $this->rawDocBlock = $reflection->getDocComment();
        $this->parameters  = array();
    }

    /**
     * @param  string $key
     *
     * @return mixed
     */
    private function parseSingle($key)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        } else {
            if (preg_match("/@" . preg_quote($key) . self::endPattern . "/", $this->rawDocBlock, $match)) {
                return true;
            } else {
                preg_match_all("/@" . preg_quote($key) . " (.*)" . self::endPattern . "/U", $this->rawDocBlock, $matches);
                $size = sizeof($matches[1]);

                // not found
                if ($size === 0) {
                    return null;
                } // found one, save as scalar
                elseif ($size === 1) {
                    return $this->parseValue($matches[1][0]);
                } // found many, save as array
                else {
                    $this->parameters[$key] = array();
                    foreach ($matches[1] as $elem) {
                        $this->parameters[$key][] = $this->parseValue($elem);
                    }

                    return $this->parameters[$key];
                }
            }
        }
    }

    private function parse()
    {
        $pattern = "/@(?=(.*)" . self::endPattern . ")/U";

        preg_match_all($pattern, $this->rawDocBlock, $matches);

        foreach ($matches[1] as $rawParameter) {
            if (preg_match("/^(" . self::keyPattern . ") (.*)$/", $rawParameter, $match)) {
                $parsedValue = $this->parseValue($match[2]);
                if (isset($this->parameters[$match[1]])) {
                    $this->parameters[$match[1]] = array_merge((array)$this->parameters[$match[1]], (array)$parsedValue);
                } else {
                    $this->parameters[$match[1]] = $parsedValue;
                }
            } else if (preg_match("/^" . self::keyPattern . "$/", $rawParameter, $match)) {
                $this->parameters[$rawParameter] = true;
            } else {
                $this->parameters[$rawParameter] = null;
            }
        }
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getVariableDeclarations($name)
    {
        $declarations = (array)$this->getParameter($name);

        foreach ($declarations as &$declaration) {
            $declaration = $this->parseVariableDeclaration($declaration, $name);
        }

        return $declarations;
    }

    private function parseVariableDeclaration($declaration, $name)
    {
        $type = gettype($declaration);

        if ($type !== 'string') {
            throw new \InvalidArgumentException("Raw declaration must be string, $type given. Key='$name'.");
        }

        if (strlen($declaration) === 0) {
            throw new \InvalidArgumentException(
                "Raw declaration cannot have zero length. Key='$name'.");
        }

        $declaration = explode(" ", $declaration);
        if (sizeof($declaration) == 1) {
            // string is default type
            array_unshift($declaration, "string");
        }

        // take first two as type and name
        $declaration = array(
            'type' => $declaration[0],
            'name' => $declaration[1],
        );

        return $declaration;
    }

    /**
     * @param  string $originalValue
     *
     * @return mixed
     */
    private function parseValue($originalValue)
    {
        if ($originalValue && $originalValue !== 'null') {
            $trimmed = trim($originalValue);
            $lower   = strtolower($trimmed);

            if (($lower === "true" || $lower === "false" || $lower === "null") && $lower !== $trimmed) {
                $value = $trimmed;
            } else if ($trimmed === "null") {
                // php 7.1 doesn't seem to decode "null" as json properly
                $value = null;
            } else if (($json = json_decode($trimmed, true)) === null) {
                // try to json decode, if cannot then store as string
                $value = $trimmed;
            } else {
                $value = $json;
            }
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Get the values of all the annotations
     *
     * @return array
     */
    public function getParameters()
    {
        if (!$this->parsedAll) {
            $this->parse();
            $this->parsedAll = true;
        }

        return $this->parameters;
    }

    /**
     * Get the value of the annotation.
     *
     * @param string $key
     *
     * @return mixed value of the annotation
     * @return null if the annotation does not exist
     */
    public function getParameter($key)
    {
        return $this->parseSingle($key);
    }

    /**
     * return the complete DocBlock
     *
     * @return string
     */
    public function getRawDocBlock()
    {
        return $this->rawDocBlock;
    }

    /**
     * @return array
     */
    public function getDescriptions()
    {
        $descriptions = [
            'short' => '',
            'long'  => '',
        ];

        $lines = explode(PHP_EOL, $this->rawDocBlock);
        foreach ($lines as $i => $line) {
            $lines[$i] = preg_replace('/^\s*\*\s*/i', '', $line);
            if ($lines[$i] == '/**' || preg_match('/^\s*\**\/$/i', $lines[$i])) {
                unset($lines[$i]);
            }
        }

        $i = 1;
        //skip leading empty lines
        while (isset($lines[$i]) && trim($lines[$i]) == '') {
            $i++;
        }

        if (isset($lines[$i])) {
            $descriptions['short'] = $lines[$i++];
        }

        //skip leading empty lines
        while (isset($lines[$i]) && trim($lines[$i]) == '') {
            $i++;
        }

        //add long-description lines
        $long = [];
        while (isset($lines[$i]) && strpos(trim($lines[$i]), '@') !== 0) {
            $long[] = $lines[$i++];
        }

        //remove trailing empty lines
        $long_reverse = array_reverse($long);
        foreach ($long_reverse as $val) {
            if (trim($val) == '') {
                array_pop($long);
            } else {
                //break on first non-empty line
                break;
            }
        }

        $descriptions['long'] = implode(PHP_EOL, $long);

        return $descriptions;
    }
}
