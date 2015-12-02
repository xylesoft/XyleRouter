<?php

namespace Xylesoft\XyleRouter\Traits;

trait RouteDefaults {

	/**
	 * Default values for tokens in a URL if the value isn't present.
	 *
	 * @param array $defaults ['pattern token'=>'value', ...]
	 * @return RouteInterface
	 */
	public function defaults(array $defaults)
	{
		$this->defaults = $defaults;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getDefaults()
	{
		return $this->defaults;
	}
}