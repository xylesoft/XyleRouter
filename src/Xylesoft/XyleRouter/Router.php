<?php

namespace Xylesoft\XyleRouter;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use Xylesoft\XyleRouter\Interfaces\RouteInterface;

/**
 * Class Router
 * @package Xylesoft\XyleRouter
 */
class Router {


//    /**
//     * @var \Xylesoft\XyleRouter\Interfaces\RequestInterface
//     */
//    protected $request;

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
     * @param string $routeClassNamespace   The fully qualified namespace of a route class which
     *                                      implements \Xylesoft\XyleRouter\Interfaces\RouteInterface.
     */
    public function __construct($routeClassNamespace) {

        $this->routeClassNamespace = $routeClassNamespace;

        // Make sure the provided route class implements the required interface.
        $implementations = class_implements($this->routeClassNamespace);

        if (! in_array('Xylesoft\XyleRouter\Interfaces\RouteInterface', $implementations)) {
            throw new \RuntimeException(
                sprintf(
                    'Route class [%s] does not implement Xylesoft\XyleRouter\Interfaces\RouteInterface',
                    $this->routeClassNamespace
                )
            );
        }
    }

    /**
     * Load the router definition into memory.
     *
     * @param string $definitionFile
     */
    public function initialize($definitionFile) {

        if (! file_exists($definitionFile)) {

            // No definition path found
            throw new \InvalidArgumentException('Route definition not found: ' . $definitionFile);
        }

        if (! is_readable($definitionFile)) {

            // No definition path found
            throw new \InvalidArgumentException('Route definition file is unreadable: ' . $definitionFile);
        }

        $router = $this;
        include $definitionFile;
    }

    /**
     * Get defined routes table.
     *
     * @return array
     */
    public function getRoutes() {

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
     * @param $regex
     * @return Route
     */
    public function route($regex) {

        $routeClass = $this->routeClassNamespace;
        $route = new $routeClass($regex, $this);

        if (! $route instanceof RouteInterface) {
            throw new \RuntimeException('Route class does not implement \Xylesoft\XyleRouter\Interfaces\RouteInterface');
        }

        $this->routes[] = $route;

        return $route;
    }


    /**
     * Attempt to match a route.
     *
     * @param RequestInterface $request
     * @return \Xylesoft\XyleRouter\Interfaces\RouteInterface|false
     */
    public function dispatch(RequestInterface $request) {

        $nonStoppedMatches = [];

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