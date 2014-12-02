<?php

namespace Xylesoft\XyleRouter\TokenMatchers;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;

class Regex extends Base {

	protected $options = [
		'pattern' => null
	];

	/**
	 * Returns true or false if one or more parameters exist in the array of parameters
	 * from the URL.
	 *
	 * @return bool
	 */
	public function match($name, $value, RequestInterface $request) {
		// TODO: Implement match() method.
	}

	/**
	 * The Regular expression pattern to be used to initially match the token.
	 *
	 * @return string
	 */
	public function getPattern() {

		return $this->options['pattern'];
	}


} 