<?php

namespace Xylesoft\XyleRouter\Traits;

use Xylesoft\XyleRouter\Interfaces\Route\RouteMethodsInterface;
use Xylesoft\XyleRouter\Interfaces\RouteInterface;

/**
 * Class HttpMethodsFacade
 *
 * @package Xylesoft\XyleRouter\Traits
 */
trait HttpMethodsFacade {

	/**
	 * Get defined routes table.
	 *
	 * @return array
	 */
	public function getRoutes() {

		return $this->routes;
	}

	/**
	 * Returns the node of a route based on the name.
	 *
	 * @param string $routeName
	 */
	public function getRouteNode($routeName) {

	}

	/**
	 * Define a new route.
	 *
	 * @param string $routePattern The route pattern.
	 * @param string $name The unique name of this route.
	 * @param array $allowedMethods The allowed HTTP methods.
	 *
	 * @return Route
	 */
	public function route($routePattern, $name, array $allowedMethods) {

		$route = $this->getRouteClass(
			$routePattern,
			$name,
			($this instanceof RouteInterface) ? $this : null
		);
		if ($route instanceof RouteMethodsInterface) {
			$route->methods($allowedMethods);
		}

		$this->routes[$route->getName()] = $route;

		return $route;
	}

	/**
	 * Define a group of sub-routes.
	 *
	 * @param string $routePattern
	 * @param string $name
	 * @param \Closure $childRoutes
	 * @return mixed
	 */
	public function group($routePattern, $name, \Closure $childRoutes) {

		$routeGroup = $this->getRouteGroupClass(
			$this->configurations,
			$routePattern,
			$name,
			$childRoutes,
			($this instanceof RouteInterface) ? $this : null
		);

		$this->routes[$routeGroup->getName()] = $routeGroup;

		return $routeGroup;
	}

	/**
	 * For matching headers.
	 *
	 * @param string $header The name of the header in the request.
	 * @param string $routePattern The pattern which should be compared against the header's value.
	 * @param srting $name The unique name of this route.
	 */
	public function header($header, $routePattern, $name) {
	}

	/**
	 * HTTP GET Route.
	 *
	 * @param string $routePattern The route pattern to match.
	 * @param string $name The unique name of this route.
	 *
	 * @return Route
	 */
	public function get($routePattern, $name) {

		return $this->route($routePattern, $name, ['GET']);
	}

	/**
	 * HTTP POST Route.
	 *
	 * @param string $routePattern The route pattern to match.
	 * @param string $name The unique name of this route.
	 *
	 * @return Route
	 */
	public function post($routePattern, $name) {

		return $this->route($routePattern, $name, ['POST']);
	}

	/**
	 * HTTP PUT Route.
	 *
	 * @param string $routePattern The route pattern to match.
	 * @param string $name The unique name of this route.
	 *
	 * @return Route
	 */
	public function put($routePattern, $name) {

		return $this->route($routePattern, $name, ['PUT']);
	}

	/**
	 * HTTP DELETE Route.
	 *
	 * @param string $routePattern The route pattern to match.
	 * @param string $name The unique name of this route.
	 *
	 * @return Route
	 */
	public function delete($routePattern, $name) {

		return $this->route($routePattern, $name, ['DELETE']);
	}

	/**
	 * HTTP HEAD Route.
	 *
	 * @param string $routePattern The route pattern to match.
	 * @param string $name The unique name of this route.
	 *
	 * @return Route
	 */
	public function head($routePattern, $name) {

		return $this->route($routePattern, $name, ['HEAD']);
	}
}