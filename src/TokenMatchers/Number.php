<?php

namespace Xylesoft\XyleRouter\TokenMatchers;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;

class Number extends Base
{
    protected $options = [
        'min' => null,
        'max' => null,
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

        if (! is_int($parameter)) {
            return false;
        }

        return $this->validateValueRange((int) $parameter);
    }

    /**
     * The pattern to be place in replacement to the token for use with a generated routing table.
     *
     * @return string
     */
    public function getInterpolationPattern()
    {
        return '\d+';
    }

    /**
     * Validates the string length.
     *
     * @param int $value
     *
     * @return bool
     */
    private function validateValueRange($value)
    {
        // Default outcomes.
        $withinMin = true;
        $withinMax = true;

        // Checking minimum value.
        if ($this->options['min'] !== null && $value < $this->options['min']) {
            $withinMin = false;
        }

        // Checking maximum value.
        if ($this->options['max'] !== null && $value > $this->options['max']) {
            $withinMax = false;
        }

        return $withinMin && $withinMax;
    }
}
