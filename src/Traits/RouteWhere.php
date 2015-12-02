<?php

namespace Xylesoft\XyleRouter\Traits;

use Xylesoft\XyleRouter\Interfaces\TokenMatcherInterface;

/**
 * Class RouteWhere
 *
 * @package Xylesoft\XyleRouter\Traits
 */
trait RouteWhere {

	/**
	 * Method for adding conditions to the tokens in a route pattern.
	 *
	 * @param string $token The name of the token in the route pattern.
	 * @param bool $optional Whether the token is optional or not, default: false
	 * @param TokenMatcherInterface $matcher A match class
	 *
	 * @return RouteInterface
	 */
	public function where($token, $optional = false, TokenMatcherInterface $matcher) {

		$this->tokens[$token] = $matcher;
		if ($optional) {
			$this->optionalTokens[] = $token;
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function getTokens() {

		return $this->tokens;
	}

	/**
	 * @return array
	 */
	public function getOptionalTokens() {

		return $this->optionalTokens;
	}
}