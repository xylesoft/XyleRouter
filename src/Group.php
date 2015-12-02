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
	 * Construct a new routing rule.
	 *
	 * @param Configurations            $configurations
	 * @param string                    $routePattern The matching pattern of the route.
	 * @param string                    $name         The unique name of the this route.
	 * @param \Closure                  $childRoutes
	 */
	public function __construct(Configurations $configurations, $routePattern, $name, \Closure $childRoutes)
	{
		$this->configurations = $configurations;
		$this->regexPattern = $routePattern;
		$this->name($name);

		$childRoutes($this);
	}

	/**
	 * Match partial route and
	 *
	 * @param RequestInterface    $request    Incoming request
	 *
	 * @return RouteInterface|bool
	 */
	public function match(RequestInterface $request) {

	}

	public function getRoutePattern() {

		return $this->parse($this->routePattern);
	}
}