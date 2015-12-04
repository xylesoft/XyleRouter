<?php

namespace Tests\PatternParsers;

use Xylesoft\XyleRouter\PatternParsers\LatinRegex;

class LatinRegexTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $parser = new LatinRegex();
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Interfaces\PatternParserInterface', $parser);
    }

    public function testYieldParameterTokensGenerator() {

//        $parser = $this->prophesize('\Xylesoft\XyleRouter\PatternParsers\LatinRegex');
//        $parser->yieldParameterTokens([],[])->shouldNotBeCalled();
//        $parser->parse('/cat', [], []);
//        $parser->parse('/(cat)', [], []);
//        $parser->parse('/[cat]', [], []);


        $parser = $this->prophesize();
        $parser->willExtend('\Xylesoft\XyleRouter\PatternParsers\LatinRegex');

        $parser->parse('/{cat}', ['cat'], []);
        $parser->yieldParameterTokens()->shouldHaveBeenCalled();
//        $parser->yieldParameterTokens([],['{/(cat)}'])->shouldBeCalled();
//        $parser->parse('/moose{/(cat)}', ['cat'], []);
    }

}
