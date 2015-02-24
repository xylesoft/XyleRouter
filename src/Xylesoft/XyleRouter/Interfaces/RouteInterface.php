<?php

namespace Xylesoft\XyleRouter\Interfaces;

use Xylesoft\XyleRouter\Router;

/**
 * Interface RouteInterface.
 */
interface RouteInterface
{
    /**
     * @param string $routePattern
     * @param Router $router
     */
    public function __construct($routePattern);

    /**
     * The allowed HTTP methods.
     *
     * @param array $allowedMethods
     *
     * @return $this
     */
    public function methods(array $allowedMethods);

    /**
     * Callback that will be called if the route matches.
     *
     * @param \Closure $callable
     *
     * @return $this
     */
    public function handle(\Closure $callable);

    /**
     * In application name of the route.
     *
     * @param $name
     *
     * @return $this
     */
    public function name($name);

    /**
     * Default values for tokens in a URL if the value isn't present.
     *
     * @param array $defaults
     */
    public function defaults(array $defaults);

    /**
     * Whether the route stops here or carries on matching.
     *
     * @param $polarity
     *
     * @return $this
     */
    public function stop($polarity);

    /**
     * Whether or not to cut out the matched portion of the URL from the current route.
     *
     * @param $polarity
     *
     * @return $this
     */
    public function cut($polarity);

    /**
     * preg_match REGEX of the route.
     *
     * @param $url
     *
     * @return RouteInterface|bool
     */
    public function match(RequestInterface $request);

    /**
     * Method for adding conditions to the tokens in a route pattern.
     *
     * @param string         $token    The name of the token in the route pattern.
     * @param bool           $optional Whether the token is optional or not, default: false
     * @param MatchInterface $matcher  A match class
     *
     * @return bool Whether the match was successful or not.
     */
    public function where($token, $optional = false, MatchInterface $matcher);

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
