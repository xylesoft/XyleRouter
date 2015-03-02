<?php

namespace Xylesoft\XyleRouter\Interfaces;


/**
 * Interface RouteInterface.
 */
interface RouteInterface
{
    /**
     * Construct a new routing rule.
     *
     * @param string $routePattern The matching pattern of the route.
     * @param string $name         The unique name of the this route.
     */
    public function __construct($routePattern, $name);

    /**
     * The allowed HTTP methods.
     *
     * @param array $allowedMethods
     *
     * @return RouteInterface
     */
    public function methods(array $allowedMethods);

    /**
     * Callback that will be called if the route matches.
     *
     * @param \Closure $callable
     *
     * @return RouteInterface
     */
    public function handle(\Closure $callable);

    /**
     * In application name of the route.
     *
     * @param $name
     *
     * @return RouteInterface
     *
     * @deprecated
     */
    public function name($name);

    /**
     * Default values for tokens in a URL if the value isn't present.
     *
     * @param array $defaults
     *
     * @return RouteInterface
     */
    public function defaults(array $defaults);

    /**
     * Whether the route stops here or carries on matching.
     *
     * @param $polarity
     *
     * @return RouteInterface
     */
    public function stop($polarity);

    /**
     * Whether or not to cut out the matched portion of the URL from the current route.
     *
     * @param $polarity
     *
     * @return RouteInterface
     */
    public function cut($polarity);

    /**
     * Perform a match routine against the request to see if the current route fulfills the outlined conditions.
     *
     * @param RequestInterface $url The request instance.
     *
     * @return RouteInterface|bool
     */
    public function match(RequestInterface $request);

    /**
     * Method for adding conditions to the tokens in a route pattern.
     *
     * @param string                $token    The name of the token in the route pattern.
     * @param bool                  $optional Whether the token is optional or not, default: false
     * @param TokenMatcherInterface $matcher  A match class
     *
     * @return RouteInterface
     */
    public function where($token, $optional = false, TokenMatcherInterface $matcher);

    /**
     * @return \Closure
     */
    public function getHandler();

    /**
     * @return array
     */
    public function getMethods();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getRoutePattern();

    /**
     * @return boolean
     */
    public function getCut();

    /**
     * @return boolean
     */
    public function getStop();
}
