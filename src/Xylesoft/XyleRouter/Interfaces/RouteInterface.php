<?php

namespace Xylesoft\XyleRouter\Interfaces;

use Xylesoft\XyleRouter\Router;


/**
 * Interface RouteInterface
 * @package Xylesoft\XyleRouter
 *
 * The single Route interface.
 */
interface RouteInterface {

    /**
     * @param string $routePattern
     * @param Router $router
     */
    public function __construct($routePattern, Router $router);

    /**
     * The allowed HTTP methods.
     *
     * @param array $allowedMethods
     * @return $this
     */
    public function methods(array $allowedMethods);

    /**
     * Callback that will be called if the route matches.
     *
     * @param \Closure $callable
     * @return $this
     */
    public function handle(\Closure $callable);

    /**
     * In application name of the route.
     *
     * @param $name
     * @return $this
     */
    public function name($name);

    /**
     * Default values for tokens in a URL if the value isn't present
     *
     * @param array $defaults
     */
    public function defaults(array $defaults);

    /**
     * @param \Xylesoft\XyleRouter\Interfaces\MatchInterface $callback
     */
    public function callback(MatchInterface $callback);

    /**
     * Whether the route stops here or carries on matching.
     *
     * @param $polarity
     * @return $this
     */
    public function stop($polarity);

    /**
     * Whether or not to cut out the matched portion of the URL from the current route.
     *
     * @param $polarity
     * @return $this
     */
    public function cut($polarity);

    /**
     * preg_match REGEX of the route
     *
     * @param $url
     * @return bool
     */
    public function match(RequestInterface $request);

    /**
     * @return \Xylesoft\XyleRouter\Interfaces\MatchInterface
     */
    public function getCallback();

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
     * @return array
     */
    public function getDefaults();

    /**
     * @return boolean
     */
    public function getCut();

    /**
     * @return boolean
     */
    public function getStop();
} 