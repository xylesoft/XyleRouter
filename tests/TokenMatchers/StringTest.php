<?php

namespace Tests\TokenMatchers;

use Tests\stubs\DummyRequest;
use Xylesoft\XyleRouter\TokenMatchers\String;

class StringTest extends \PHPUnit_Framework_TestCase {

	public function testInstance() {

		$matcher = new String();
		$this->assertInstanceOf('\Xylesoft\XyleRouter\Interfaces\TokenMatcherInterface', $matcher);
	}

	public function testReturnedPattern() {

		$matcher = new String;
		$this->assertEquals('[^\/]+', $matcher->getInterpolationPattern());
	}

	public function testStringLengthParameters() {

		$mockRequest = new DummyRequest('/nothing-to-see');
		$matcher = new String(['min' => 2, 'max' => 10]);

		$tooShortResult = $matcher->match('to_short', 'A', $mockRequest);
		$this->assertFalse($tooShortResult);
		$tooLongResult = $matcher->match('to_long', 'ABCDEFGHIJKLMN', $mockRequest);
		$this->assertFalse($tooLongResult);
		$shortEdgeResult = $matcher->match('short_edge', 'AA', $mockRequest);
		$this->assertTrue($shortEdgeResult);
		$longEndgeResult = $matcher->match('long_edge', 'AABBCCDDEE', $mockRequest);
		$this->assertTrue($longEndgeResult);
		$inbetweenResult = $matcher->match('inbetween', 'ABCDE', $mockRequest);
		$this->assertTrue($inbetweenResult);
	}

	public function testStringWithUTF8Chars() {

		$mockRequest = new DummyRequest('/nothing-to-see');
		$matcher = new String(['min' => 2, 'max' => 10]);

		$tooShortResult = $matcher->match('to_short', '🍓', $mockRequest); // Strawberry
		$this->assertFalse($tooShortResult);
		$tooLongResult = $matcher->match('to_long', '絵文字絵文字絵文字絵文字', $mockRequest);
		$this->assertFalse($tooLongResult);
		$shortEdgeResult = $matcher->match('short_edge', '絵文', $mockRequest);
		$this->assertTrue($shortEdgeResult);
		$longEndgeResult = $matcher->match('long_edge', '絵文字絵文字絵文字字', $mockRequest);
		$this->assertTrue($longEndgeResult);
		$inbetweenResult = $matcher->match('inbetween', '絵文字絵文', $mockRequest);
		$this->assertTrue($inbetweenResult);
	}
}
