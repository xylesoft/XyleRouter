<?php

namespace Xylesoft\XyleRouter\Interfaces\Route;


interface RouteStoppingInterface {

	/**
	 * Whether the matched route stops here or carries on down the routing table.
	 *
	 * @param $polarity
	 *
	 * @return RouteInterface
	 */
	public function stop($polarity);

	/**
	 * @return boolean
	 */
	public function getStop();
}