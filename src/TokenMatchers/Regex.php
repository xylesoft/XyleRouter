<?php

namespace Xylesoft\XyleRouter\TokenMatchers;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;

class Regex extends Base
{
    protected $options = [
        'pattern' => null,
    ];

    /**
     * Returns true or false if one or more parameters exist in the array of parameters
     * from the URL.
     *
     * @param string $name The name of the parameter in the request.
     * @param mixed $parameter Parameter value for matching.
     * @param RequestInterface $request The current request instance.
     *
     * @return bool
     */
    public function match($name, $parameter, RequestInterface $request)
    {
        // Interpolation pattern matched, so therefore regex succeeded.
        return true;
    }

    /**
     * The pattern to be place in replacement to the token for use with a generated routing table.
     *
     * @return string
     */
    public function getInterpolationPattern()
    {
        return $this->options['pattern'];
    }
}
