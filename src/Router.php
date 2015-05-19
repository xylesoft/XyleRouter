<?php

namespace Xylesoft\XyleRouter;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use Xylesoft\XyleRouter\Interfaces\RouteInterface;
use Xylesoft\XyleRouter\PatternParsers\LatinRegex;

/**
 * Class Router.
 */
class Router
{
    /**
     * @var string
     */
    protected $routeClassNamespace;

    /**
     * The array of all the routes.
     *
     * @var array of \Xylesoft\XyleRouter\Route
     */
    protected $routes;

    /**
     * @var PatternParsers\LatinRegex
     */
    protected $patternParser;

    /**
     * @var array Router configurations:
     * @property string route_class_namespace           The Route class, used in method: route()
     * @property string route_group_class_namespace     The Route Grouping class, used in method: group()
     * @property string pattern_parser_class_namespace  The Route Grouping class, used in method: group()
     */
    protected $configuration = [

    ];

    /**
     * @param string $routeClassNamespace The fully qualified namespace of a route class which
     *                                    implements \Xylesoft\XyleRouter\Interfaces\RouteInterface.
     * @param string $definitionFile      Optionally initialize the definition during construct.
     */
    public function __construct($routeClassNamespace, $definitionFile = null)
    {
        // define the Route class name
        $this->register('route_class_namespace', '\Xylesoft\XyleRouter\Route');
        $this->register('route_group_class_namespace', '\Xylesoft\XyleRouter\RouteGroup');
        $this->register('header_class_namespace', '\Xylesoft\XyleRouter\Header');
//        $this->routeClassNamespace = $routeClassNamespace;

        // define the Pattern Building Parser
        $this->register('pattern_parser_class_namespace', new LatinRegex());
//        $this->patternParser = new LatinRegex();

        // Initialize a definition file if provided.
        if ($definitionFile) {
            $this->initialize($definitionFile);
        }
    }

    public function register($config, $value)
    {
        $this->configuration[$config] = $value;
    }

    /**
     * Load the router definition into memory.
     *
     * @param string $definitionFile
     */
    public function initialize($definitionFile)
    {
        if (! file_exists($definitionFile)) {

            // No definition path found
            throw new \InvalidArgumentException('Route definition not found: '.$definitionFile);
        }

        if (! is_readable($definitionFile)) {

            // No definition path found
            throw new \InvalidArgumentException('Route definition file is unreadable: '.$definitionFile);
        }

        $router = $this;
        include $definitionFile;
    }

    /**
     * Get defined routes table.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

//    /**
//     * Set the routes table from a cache
//     */
//    public function setRoutes() {
//
//    }

    /**
     * Define a new route.
     *
     * @param string $routePattern   The route pattern.
     * @param string $name           The unique name of this route.
     * @param array  $allowedMethods The allowed HTTP methods.
     *
     * @return Route
     */
    public function route($routePattern, $name, array $allowedMethods)
    {
        $routeClass = $this->configuration['route_class_namespace'];
        $route = new $routeClass($routePattern, $name, $this->configuration['pattern_parser_class_namespace']);

        if (! $route instanceof RouteInterface) {
            throw new \RuntimeException('Route class does not implement \Xylesoft\XyleRouter\Interfaces\RouteInterface');
        }

        $route->methods($allowedMethods);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * HTTP GET Route.
     *
     * @param string $routePattern The route pattern to match.
     * @param string $name         The unique name of this route.
     *
     * @return Route
     */
    public function get($routePattern, $name)
    {
        return $this->route($routePattern, $name, ['GET']);
    }

    /**
     * HTTP POST Route.
     *
     * @param string $routePattern The route pattern to match.
     * @param string $name         The unique name of this route.
     *
     * @return Route
     */
    public function post($routePattern, $name)
    {
        return $this->route($routePattern, $name, ['POST']);
    }

    /**
     * HTTP PUT Route.
     *
     * @param string $routePattern The route pattern to match.
     * @param string $name         The unique name of this route.
     *
     * @return Route
     */
    public function put($routePattern, $name)
    {
        return $this->route($routePattern, $name, ['PUT']);
    }

    /**
     * HTTP DELETE Route.
     *
     * @param string $routePattern The route pattern to match.
     * @param string $name         The unique name of this route.
     *
     * @return Route
     */
    public function delete($routePattern, $name)
    {
        return $this->route($routePattern, $name, ['DELETE']);
    }

    /**
     * HTTP HEAD Route.
     *
     * @param string $routePattern The route pattern to match.
     * @param string $name         The unique name of this route.
     *
     * @return Route
     */
    public function head($routePattern, $name)
    {
        return $this->route($routePattern, $name, ['HEAD']);
    }

    /**
     * For matching headers.
     *
     * @param string $header       The name of the header in the request.
     * @param string $routePattern The pattern which should be compared against the header's value.
     * @param srting $name         The unique name of this route.
     */
    public function header($header, $routePattern, $name)
    {
    }

    public function group($routePattern, $name, callable $childRoutes)
    {
        $routeClass = $this->configuration['route_group_class_namespace'];
        $routeGroup = new $routeClass($routePattern, $name, $this->configuration['pattern_parser_class_namespace']);

        if (! $routeGroup instanceof RouteGroupInterface) {
            throw new \RuntimeException('Route class does not implement \Xylesoft\XyleRouter\Interfaces\RouteGroupInterface');
        }

        $this->routes[] = $routeGroup;

        return $routeGroup;
    }

    /**
     * Attempt to match a route.
     *
     * @param RequestInterface $request
     *
     * @return \Xylesoft\XyleRouter\Interfaces\RouteInterface|false
     */
    public function dispatch(RequestInterface $request)
    {
        $nonStoppedMatches = [];

        // @TODO Use immutable request type object for running the route matching chain, so the path can be manipulated.

        foreach ($this->routes as $route) {
            if ($match = $route->match($request)) {
                if ($match->getStop() === true) {

                    // We've found our route.
                    return $match;
                } else {

                    // Carry on, but preserve the information aquired from the non-stop route.
                    $nonStoppedMatches[] = $match;
                }
            }
        }

        // No match
        return false;
    }
}
