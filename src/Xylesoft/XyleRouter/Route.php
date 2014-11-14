<?php

namespace Xylesoft\XyleRouter;

use Xylesoft\XyleRouter\Interfaces\MatchInterface;
use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use Xylesoft\XyleRouter\Interfaces\RouteInterface;

/**
 * Class Route
 *
 * A single route definition class.
 *
 * @package Xylesoft\XyleRouter
 */
class Route implements RouteInterface {

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $routePattern;

    /**
     * @var array
     */
    protected $methods;

    /**
     * @var \Closure
     */
    protected $handler;

    /**
     * @var \Xylesoft\XyleRouter\Interfaces\MatchInterface
     */
    protected $callback;


    /**
     * @var string  Unique name of the route.
     */
    protected $name;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var bool
     */
    protected $stop = true;

    /**
     * @var bool
     */
    protected $cut = false;

    /**
     * @param Router $router
     */
    public function __construct($routePattern, Router $router) {

        $this->router = $router;
        $this->routePattern = $this->parse($routePattern);
    }

    /**
     * Responsible with converting the incoming route into a preg_* compatible regular expression.
     *
     * @param string $pattern
     * @return string
     */
    private function parse($pattern) {

        $parameters = $optionalParameters = [];

        // Extract the simple '(token:pattern)' parameters and turn into valid REGEX.
        if (preg_match('#\([a-zA-Z0-9]+:#', $pattern)) {
            preg_match_all('#\([a-zA-Z0-9]+:[^{^}]+\)#', $pattern, $parameters);

            if (count($parameters) === 0) {
                throw new \InvalidArgumentException(sprintf('One or more route parameter patterns (token:regex) is invalid: %s', $pattern));
            }
        }

        // Extract the optional '{(token:pattern)}' parameters and turn into valid REGEX.
        if (preg_match('#{#', $pattern)) {
            preg_match_all('#{.+\(\s+:.+\).+}#', $pattern, $optionalParameters);

            if (count($optionalParameters) === 0) {
                throw new \InvalidArgumentException(sprintf('One or more optional route Patterns {(token:regex)} is invalid: %s', $pattern));
            }
        }

        return '#' . $pattern . '#';
    }

    /**
     * The allowed HTTP methods.
     *
     * @param array $allowedMethods
     * @return $this
     */
    public function methods(array $allowedMethods) {

        $this->methods = $allowedMethods;
        return $this;
    }

    /**
     * Callback that will be called if the route matches.
     *
     * @param callable $callable
     * @return $this
     */
    public function handle(\Closure $callable) {

        $this->handler = $callable;
        return $this;
    }

    /**
     * In application name of the route.
     *
     * @param $name
     * @return $this
     */
    public function name($name) {

        $this->name = $name;
        return $this;
    }


    /**
     * Default values for tokens in a URL if the value isn't present
     *
     * @param array $defaults
     */
    public function defaults(array $defaults) {

        $this->defaults  = $defaults;
        return $this;
    }

    /**
     * @param \Xylesoft\XyleRouter\Interfaces\MatchInterface $callback
     */
    public function callback(MatchInterface $callback) {

        $this->callback = $callback;
        return $this;
    }

    /**
     * Whether the route stops here or carries on matching.
     *
     * @param $polarity
     * @return $this
     */
    public function stop($polarity) {

        $this->stop = $polarity;
        return $this;
    }

    /**
     * Whether or not to cut out the matched portion of the URL from the current route.
     *
     * @param $polarity
     * @return $this
     */
    public function cut($polarity) {

        $this->cut = $polarity;
        return $this;
    }

    /**
     * preg_match REGEX of the route
     *
     * @param $url
     * @return RouteInterface|bool
     */
    public function match(RequestInterface $request) {

        $url = $request->getUrl();
        $pattern = $this->getRoutePattern();

        return (preg_match($pattern, $url)) ? $this : false;
    }

    /************************************
     * GETTER METHODS
     ************************************/

    /**
     * @return \Xylesoft\XyleRouter\Interfaces\MatchInterface
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRoutePattern()
    {
        return $this->routePattern;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @return boolean
     */
    public function getCut()
    {
        return $this->cut;
    }

    /**
     * @return boolean
     */
    public function getStop()
    {
        return $this->stop;
    }


} 