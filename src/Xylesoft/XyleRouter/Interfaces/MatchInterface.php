<?php

namespace Xylesoft\XyleRouter\Interfaces;

/**
 * Interface MatchInterface
 *
 * Callback interface for matching routes with dynamic tokens.
 *
 * @package Xylesoft\XyleRouter\Interfaces
 */
interface MatchInterface {


    /**
     * Returns true or false if one or more parameters exist in the array of parameters
     * from the URL.
     *
     * @return bool
     */
    public function match(array $parameters, RequestInterface $request);

}