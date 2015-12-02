<?php

namespace Xylesoft\XyleRouter\Interfaces;


/**
 * Interface RouteInterface.
 */
interface RouteInterface
{
    /**
     * Perform a match routine against the request to see if the current route fulfills the outlined conditions.
     *
     * @param RequestInterface $request The request instance.
     *
     * @return RouteInterface|bool
     */
    public function match(RequestInterface $request);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getRoutePattern();
}
