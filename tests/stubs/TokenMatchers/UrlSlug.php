<?php

namespace Tests\stubs\TokenMatchers;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use \Xylesoft\XyleRouter\TokenMatchers\Base;

class UrlSlug extends Base {

	private $stubUrlSlugs = [
		'playstation-4-hack',
		'cat-and-dog-collars',
		'fallout-4-deal'
	];

	public function match($name, $parameter, RequestInterface $request) {

		return in_array($parameter, $this->stubUrlSlugs);
	}

	public function getInterpolationPattern() {

		return '[a-zA-Z0-9_\-]+';
	}

}