<?php

namespace Xylesoft\XyleRouter\Interfaces;

/**
 * Interface TokenMatcherInterface.
 *
 * Callback interface responsible for matching and providing an interpolation for an individual route token.
 */
interface TokenMatcherInterface
{
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
    public function match($name, $parameter, RequestInterface $request);

    /**
     * The pattern to be place in replacement to the token for use with a generated routing table.
     *
     * @return string
     */
    public function getInterpolationPattern();
}
