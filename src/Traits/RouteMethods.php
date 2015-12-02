<?php

namespace Xylesoft\XyleRouter\Traits;


trait RouteMethods {

	/**
	 * The allowed HTTP methods.
	 *
	 * @param array $allowedMethods
	 *
	 * @return RouteInterface
	 */
	public function methods(array $allowedMethods)
	{
		$this->methods = $allowedMethods;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getMethods()
	{
		return $this->methods;
	}
}