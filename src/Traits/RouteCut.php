<?php

namespace Xylesoft\XyleRouter\Traits;

trait RouteCut {

	/**
	 * Whether or not to cut out the matched portion of the URL from the current route.
	 *
	 * @param $polarity
	 *
	 * @return RouteInterface
	 */
	public function cut($polarity)
	{
		$this->cut = $polarity;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getCut()
	{
		return $this->cut;
	}
}