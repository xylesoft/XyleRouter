<?php

namespace Xylesoft\XyleRouter\TokenMatchers;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;

class String extends Base
{
    protected $options = [
        'min' => null,      // Minimum string Length
        'max' => null,      // Maximum string Length
    ];

    /**
     * Returns true or false if one or more parameters exist in the array of parameters
     * from the URL.
     *
     * @param string           $name      The name of the parameter in the request.
     * @param mixed            $parameter Parameter value for matching.
     * @param RequestInterface $request   The current request instance.
     *
     * @return bool
     */
    public function match($name, $parameter, RequestInterface $request)
    {

        // check the length.
        return $this->validateStringLength($parameter);
    }

    /**
     * The pattern to be place in replacement to the token for use with a generated routing table.
     *
     * @return string
     */
    public function getInterpolationPattern()
    {
        return '[^\/]+';
    }

    /**
     * Validates the string length.
     *
     * @param string $value The string to be checked.
     *
     * @return bool
     */
    private function validateStringLength($value)
    {
        // Default outcomes.
        $withinMin = true;
        $withinMax = true;

        // Checking minimum length
        if ($this->options['min'] !== null && mb_strlen($value) < $this->options['min']) {
            $withinMin = false;
        }

        // Checking maximum length.
        if ($this->options['max'] !== null && mb_strlen($value) > $this->options['max']) {
            $withinMax = false;
        }

        return $withinMin && $withinMax;
    }
}
