<?php

namespace Xylesoft\XyleRouter\Interfaces\Route;

interface RouteCuttingInterface {

	/**
	 * Whether or not to cut out the matched portion of the URL from the current route.
	 *
	 * @param $polarity
	 *
	 * @return RouteInterface
	 */
	public function cut($polarity);

	/**
	 * @return boolean
	 */
	public function getCut();
}