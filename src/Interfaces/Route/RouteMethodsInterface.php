<?php

namespace Xylesoft\XyleRouter\Interfaces\Route;


interface RouteMethodsInterface {

	/**
	 * The allowed HTTP methods.
	 *
	 * @param array $allowedMethods
	 *
	 * @return RouteInterface
	 */
	public function methods(array $allowedMethods);

	/**
	 * @return array
	 */
	public function getMethods();
}