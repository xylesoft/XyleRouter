<?php

namespace Xylesoft\XyleRouter;

use Xylesoft\XyleRouter\Configuration\Configurations;
use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteStoppingInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteInterface;
use Xylesoft\XyleRouter\Traits\HttpMethodsFacade;
use Xylesoft\XyleRouter\Traits\RouteClasses;

/**
 * Class Router.
 */
class Router {

	use HttpMethodsFacade;
	use RouteClasses;

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
	 * @var Configurations
	 */
	protected $configurations;

	/**
	 * @param Configurations $configurations
	 * @param string $definitionFile Optionally initialize the definition during construct.
	 */
	public function __construct(Configurations $configurations, $definitionFile = null) {

		$this->configurations = $configurations;

		// Makes sure all the configured classes implement the correct interfaces.
		$this->validateRouteClasses();

		// Initialize a definition file if provided.
		if ($definitionFile) {
			$this->initialize($definitionFile);
		}
	}

	protected function validateRouteClasses() {

		if (!$this->getRouteClass('nothing', 'testing') instanceof RouteInterface) {
			throw new \RuntimeException('config: route_class_namespace class does not implement \Xylesoft\XyleRouter\Interfaces\Route\RouteInterface');
		}

		if (!$this->getRouteGroupClass($this->configurations, 'nothing', 'testing', function($router) {}, null) instanceof RouteInterface) {
			throw new \RuntimeException('config: route_group_class_namespace class does not implement \Xylesoft\XyleRouter\Interfaces\Route\RouteInterface');
		}

		//		if (!$this->getRouteHeaderClass('nothing', 'testing') instanceof RouteInterface) {
		//			throw new \RuntimeException('config: route_group_class_namespace class does not implement \Xylesoft\XyleRouter\Interfaces\RouteGroupInterface');
		//		}
	}

	/**
	 * Load the router definition into memory.
	 *
	 * @param string $definitionFile
	 */
	public function initialize($definitionFile) {

		if (!file_exists($definitionFile)) {

			// No definition path found
			throw new \InvalidArgumentException('Route definition not found: ' . $definitionFile);
		}

		if (!is_readable($definitionFile)) {

			// No definition path found
			throw new \InvalidArgumentException('Route definition file is unreadable: ' . $definitionFile);
		}

		$router = $this;
		include $definitionFile;
	}

	/**
	 * Attempt to match a route.
	 *
	 * @param RequestInterface $request
	 *
	 * @return \Xylesoft\XyleRouter\Interfaces\Route\RouteInterface|false
	 */
	public function dispatch(RequestInterface $request) {

		$nonStoppedMatches = [];

		// @TODO Use immutable request object for running the route matching chain, so the path can be manipulated.

		foreach ($this->routes as $route) {
			if ($match = $route->match($request)) {
				if ($match instanceof RouteStoppingInterface && $match->getStop() === true) {

					// We've found our route.
					return $match;
				} else {

					// Carry on, but preserve the information acquired from the non-stop route.
					$nonStoppedMatches[] = $match;
				}
			}
		}

		// No match
		return false;
	}

	/**
	 * Generate a URL based on a route name.
	 *
	 * @param string $name
	 * @param array $parameters
	 * @param bool|true $relativePath
	 * @return string
	 *
	 * @throws InvalidRouteNameException
	 */
	public function generateUrl($name, $parameters, $relativePath = true) {

	}
}
