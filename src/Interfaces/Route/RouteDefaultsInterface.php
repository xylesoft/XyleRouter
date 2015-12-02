<?php

namespace Xylesoft\XyleRouter\Interfaces\Route;

interface RouteDefaultsInterface {

	/**
	 * Default values for tokens in a URL if the value isn't present.
	 *
	 * @param array $defaults ['pattern token'=>'value', ...]
	 */
	public function defaults(array $defaults);

	/**
	 * @return array
	 */
	public function getDefaults();
}