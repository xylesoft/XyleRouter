<?php

namespace Xylesoft\XyleRouter\Interfaces\Route;


use Xylesoft\XyleRouter\Interfaces\TokenMatcherInterface;

interface RouteWhereInterface {

	/**
	 * Method for adding conditions to the tokens in a route pattern.
	 *
	 * @param string $token The name of the token in the route pattern.
	 * @param bool $optional Whether the token is optional or not, default: false
	 * @param TokenMatcherInterface $matcher A match class
	 *
	 * @return RouteInterface
	 */
	public function where($token, $optional = false, TokenMatcherInterface $matcher);
}