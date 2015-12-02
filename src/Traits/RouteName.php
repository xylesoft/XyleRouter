<?php

namespace Xylesoft\XyleRouter\Traits;

trait RouteName {

	/**
	 * In application name of the route.
	 *
	 * @param $name
	 *
	 * @return RouteInterface
	 */
	public function name($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}