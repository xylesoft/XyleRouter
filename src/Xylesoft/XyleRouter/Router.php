<?php

namespace Xylesoft\XyleRouter;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use Xylesoft\XyleRouter\Route;

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
     * The array of all the routes.
     *
     * @var array of \Xylesoft\XyleRouter\Route
     */
    protected $routes;

    /**
     * Load the router definition into memory.
     *
     * @param string $defintionFile
     */
    public function initialize($defintionFile) {

        if (! file_exists($defintionFile)) {

            // No definition path found
            throw new \InvalidArgumentException('Route definition not found: ' . $defintionFile);
        }

        if (! is_readable($defintionFile)) {

            // No definition path found
            throw new \InvalidArgumentException('Route definition file is unreadable: ' . $defintionFile);
        }

        $router = $this;
        include $defintionFile;
    }

    public function getRoutes() {

        return $this->routes;
    }

    public function route($regex) {

        $route = new Route($regex, $this);
        $this->routes[] = $route;
        return $route;
    }


    /**
     * Attempt to match a route.
     *
     * @param RequestInterface $request
     * @return \Xylesoft\XyleRouter\Route|false
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