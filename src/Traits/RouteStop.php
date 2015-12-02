<?php

namespace Xylesoft\XyleRouter\Traits;

trait RouteStop {

	/**
	 * Whether the matched route stops here or carries on down the routing table.
	 *
	 * @param $polarity
	 *
	 * @return RouteInterface
	 */
	public function stop($polarity)
	{
		$this->stop = $polarity;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getStop()
	{
		return $this->stop;
	}
}