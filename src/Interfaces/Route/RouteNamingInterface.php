<?php

namespace Xylesoft\XyleRouter\Interfaces\Route;

interface RouteNamingInterface {
	/**
	 * In application name of the route.
	 *
	 * @param $name
	 *
	 * @return RouteInterface
	 */
	public function name($name);

	/**
	 * @return string
	 */
	public function getName();
}