<?php

namespace Xylesoft\XyleRouter\TokenMatchers;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;

class Number extends Base {

	protected $options = [
		'min' => null,
		'max' => null,
		'notations' => false
	];

	/**
	 * Returns true or false if one or more parameters exist in the array of parameters
	 * from the URL.
	 *
	 * @return bool
	 */
	public function match(array $parameters, RequestInterface $request) {
		// TODO: Implement match() method.
	}

	/**
	 * The Regular expression pattern to be used to initially match the token.
	 *
	 * @return string
	 */
	public function getPattern() {

		return '\d+';
	}
}