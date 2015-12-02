<?php

namespace Xylesoft\XyleRouter;

use Xylesoft\XyleRouter\Configuration\Configurations;
use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteCuttingInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteDefaultsInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteMethodsInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteNamingInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteStoppingInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteWhereInterface;
use Xylesoft\XyleRouter\Interfaces\RouteInterface;
use Xylesoft\XyleRouter\Traits\HttpMethodsFacade;
use Xylesoft\XyleRouter\Traits\RouteClasses;
use Xylesoft\XyleRouter\Traits\RouteCut;
use Xylesoft\XyleRouter\Traits\RouteDefaults;
use Xylesoft\XyleRouter\Traits\RouteMethods;
use Xylesoft\XyleRouter\Traits\RouteName;
use Xylesoft\XyleRouter\Traits\RouteStop;
use Xylesoft\XyleRouter\Traits\RouteWhere;

class Group implements RouteInterface, RouteCuttingInterface, RouteStoppingInterface, RouteDefaultsInterface, RouteMethodsInterface, RouteWhereInterface, RouteNamingInterface {

	use HttpMethodsFacade;
	use RouteClasses;
	use RouteName, RouteStop, RouteCut, RouteDefaults, RouteMethods, RouteWhere;

	/**
	 * The current route as a full regular expression.
	 *
	 * @var string
	 */
	protected $regexPattern = null;

	/**
	 * Array of tokens and the rules which apply to the token.
	 *
	 * @var array
	 */
	protected $tokens = [];

	/**
	 * Tokens which are considered optional in the pattern.
	 *
	 * @var array
	 */
	protected $optionalTokens = [];

	/**
	 * The allowed HTTP methods for the current route.
	 *
	 * @var array
	 */
	protected $methods;

	/**
	 * @var array
	 */
	protected $routes = [];

	/**
	 * @var Configurations
	 */
	protected $configurations;

	/**
	 * @var RouteInterface
	 */
	protected $parent;

	/**
	 * Construct a new routing rule.
	 *
	 * @param Configurations            $configurations
	 * @param string                    $routePattern The matching pattern of the route.
	 * @param string                    $name         The unique name of the this route.
	 * @param RouteInterface|null       $parent            The route's parent if exists.
	 * @param \Closure                  $childRoutes
	 */
	public function __construct(Configurations $configurations, $routePattern, $name, RouteInterface $parent = null, \Closure $childRoutes)
	{
		$this->configurations = $configurations;
		$this->regexPattern = $routePattern;
		$this->parent = $parent;
		$this->name($name);

		$childRoutes($this);
	}

	/**
	 * @return RouteInterface
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * Match partial route and
	 *
	 * @param RequestInterface    $request    Incoming request
	 *
	 * @return RouteInterface|bool
	 */
	public function match(RequestInterface $request) {

		foreach ($this->routes as $key => $route) {

			$resultFromRoute = $route->match($request);
			if ($resultFromRoute instanceof RouteInterface) {
				return $resultFromRoute;
			}
		}

		return false;
	}

	public function getRoutePattern() {

		$pattern = $this->regexPattern;
		if ($this->getParent() instanceof RouteInterface) {
			$parentRoutePattern = $this->getParent()->getRoutePattern();

			// Clean up incoming route of $ clauses etc.
			if (mb_substr($parentRoutePattern, -1) === '$') {
				$parentRoutePattern = mb_substr($parentRoutePattern, mb_strlen($parentRoutePattern) - 1);
			}

			// Clean up current routes beginning of string symbol ^.
			if (mb_substr($pattern, 0, 1) === '^') {
				$pattern = mb_substr($pattern, 1);
			}

			$pattern = $parentRoutePattern . $pattern;
		}

		return $pattern;
	}

}