<?php

namespace Tests\stubs\TokenMatchers;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use \Xylesoft\XyleRouter\TokenMatchers\Base;

class ThreadId extends Base {

	private $stubThreadIds = [
		1, 20, 300, 4000, 54321
	];

	public function match($name, $parameter, RequestInterface $request) {

		return in_array($parameter, $this->stubThreadIds);
	}

	public function getInterpolationPattern() {

		return '\d+';
	}

}