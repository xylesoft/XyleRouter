<?php

namespace Xylesoft\XyleRouter\Abstracts;


use Xylesoft\XyleRouter\Interfaces\RouteInterface;
use Xylesoft\XyleRouter\PatternParsers\LatinRegex;

abstract class RouteAbstract implements RouteInterface {

    /**
     * The allowed HTTP methods for the current route.
     *
     * @var array
     */
    protected $methods;

    /**
     * The handler of a successfully matched and stopped route.
     *
     * @var \Closure
     */
    protected $handler;

    /**
     * @var \Xylesoft\XyleRouter\Interfaces\TokenMatcherInterface
     */
    protected $callback;

    /**
     * Unique name of the route.
     *
     * @var string
     */
    protected $name;

    /**
     * The default value to be interpolated into a token if the token isn't provided a value.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Whether to stop on this route if match was successful or not.
     *
     * @var bool
     */
    protected $stop = true;

    /**
     * Strip out the current route pattern before allowing the routing request to move to the next route check.
     *
     * @var bool
     */
    protected $cut = false;

    /**
     * The current route pattern.
     *
     * @var string
     */
    protected $routePattern;

    /**
     * The pattern builder.
     *
     * @var \Xylesoft\XyleRouter\PatternParsers\LatinRegex
     */
    protected $parser;

    /**
     * Construct a new routing rule.
     *
     * @param string        $routePattern The matching pattern of the route.
     * @param string        $name         The unique name of the this route.
     * @param LatinRegex $parser
     */
    public function __construct($routePattern, $name, LatinRegex $parser = null)
    {
        $this->routePattern = $routePattern;
        $this->name($name);

        $this->parser = ($parser) ?: new LatinRegex();
    }

} 