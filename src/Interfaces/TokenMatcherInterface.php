<?php

namespace Xylesoft\XyleRouter\Interfaces;

/**
 * Interface TokenMatcherInterface.
 *
 * Callback interface for matching a token in a route.
 */
interface MatchInterface
{
    /**
     * Returns true or false if one or more parameters exist in the array of parameters
     * from the URL.
     *
     * @return bool
     */
    public function match($name, $value, RequestInterface $request);

    /**
     * The Regular expression pattern to be used to initially match the token.
     *
     * @return string
     */
    public function getPattern();
}
