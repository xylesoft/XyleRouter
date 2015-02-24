<?php

namespace Xylesoft\XyleRouter\Interfaces;

/**
 * Interface RequestInterface.
 *
 * Wrapper class to communicate between your implemented request instance and
 * the Router.
 */
interface RequestInterface
{
    /**
     * Get the URL provided in the request.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Get the GET parameters from the Request.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Get a single GET parameter from the request.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter($name);

    /**
     * Get all the request Headers.
     *
     * @return array
     */
    public function getHeaders();

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setParameter($name, $value);
}
