<?php

namespace Tests\stubs\TokenMatchers;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use \Xylesoft\XyleRouter\TokenMatchers\Base;

class ThreadType extends Base {

	private $stubThreadTypes = [
		'deal', 'voucher'
	];

	public function match($name, $parameter, RequestInterface $request) {

		return in_array($parameter, $this->stubThreadTypes);
	}

	public function getInterpolationPattern() {

		return '\S+';
	}

}