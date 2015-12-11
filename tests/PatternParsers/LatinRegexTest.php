<?php

namespace Tests\PatternParsers;

use Xylesoft\XyleRouter\PatternParsers\LatinRegex;

class LatinRegexTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $parser = new LatinRegex();
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Interfaces\PatternParserInterface', $parser);
    }

    public function testYieldParameterTokensGenerator() {

        $stringMatcher = $this->prophesize('\Xylesoft\XyleRouter\TokenMatchers\String');
        $stringMatcher->getInterpolationPattern()->willReturn('\S+')->shouldBeCalled();
        $stringMatcher->match()->shouldNotBeCalled();
        $stringMatcher->validateStringLength()->shouldNotBeCalled();

        $parser = new LatinRegex;

        // Test simple parameter is matched, without start/end anchor.
        $patternMatchers = $parser->parse('/{cat}', ['cat' => $stringMatcher->reveal()], []);
        $this->assertEquals('#\/(?P<cat>\S+)#', $patternMatchers);

        // Test simple parameter is matched, with start/end anchor.
        $patternMatchers = $parser->parse('^/{cat}$', ['cat' => $stringMatcher->reveal()], []);
        $this->assertEquals('#^\/(?P<cat>\S+)$#', $patternMatchers);

        // Test simple parameter which is optional, without start/end anchors
        $patternMatchers = $parser->parse('/{cat}', [], ['cat' => $stringMatcher->reveal()]);
        $this->assertEquals('#\/((?P<cat>\S+))?#', $patternMatchers);

        // Test simple parameter which is optional, with start/end anchors
        $patternMatchers = $parser->parse('^/{cat}$', [], ['cat' => $stringMatcher->reveal()]);
        $this->assertEquals('#^\/((?P<cat>\S+))?$#', $patternMatchers);

        // Test a complex pattern, which must capture an extra string containing a parameter. Without start/end anchor.
        $patternMatchers = $parser->parse('/moose{/with-a-(cat)}', ['cat' => $stringMatcher->reveal()], []);
        $this->assertEquals('#\/moose\/with-a-(?P<cat>\S+)#', $patternMatchers);

        // Test a complex pattern, which must capture an extra string containing a parameter. With start/end anchor.
        $patternMatchers = $parser->parse('^/moose{/with-a-(cat)}$', ['cat' => $stringMatcher->reveal()], []);
        $this->assertEquals('#^\/moose\/with-a-(?P<cat>\S+)$#', $patternMatchers);

        // Test simple and complex parameters, without start/end anchor.
        $patternMatchers = $parser->parse('/moose/{dog}{/with-a-(cat)}', ['cat' => $stringMatcher->reveal(), 'dog' => $stringMatcher->reveal()], []);
        $this->assertEquals('#\/moose\/(?P<dog>\S+)\/with-a-(?P<cat>\S+)#', $patternMatchers, 'on: /moose/{dog}{/with-a-(cat)}');

        // Test simple and complex parameters, with start/end anchor.
        $patternMatchers = $parser->parse('^/moose/{dog}{/with-a-(cat)}$', ['cat' => $stringMatcher->reveal(), 'dog' => $stringMatcher->reveal()], []);
        $this->assertEquals('#^\/moose\/(?P<dog>\S+)\/with-a-(?P<cat>\S+)$#', $patternMatchers, 'on: ^/moose/{dog}{/with-a-(cat)}$');

        // Test simple parameter, with a complex optional parameter. Without start/end anchor.
        $patternMatchers = $parser->parse('/moose/{dog}{/with-a-(cat)}', ['dog' => $stringMatcher->reveal()], ['cat' => $stringMatcher->reveal()]);
        $this->assertEquals('#\/moose\/(?P<dog>\S+)(\/with-a-(?P<cat>\S+))?#', $patternMatchers, 'on (with optional dog param): ^/moose/{dog}{/with-a-(cat)}$');

        // Test simple parameter, with a complex optional parameter. With start/end anchor.
        $patternMatchers = $parser->parse('^/moose/{dog}{/with-a-(cat)}$', ['cat' => $stringMatcher->reveal()], ['dog' => $stringMatcher->reveal()]);
        $this->assertEquals('#^\/moose\/((?P<dog>\S+))?\/with-a-(?P<cat>\S+)$#', $patternMatchers, 'on (with optional dog param): ^/moose/{dog}{/with-a-(cat)}$');

        // Test simple and complex parameters where the complex parameter has suffixed text in the {}, without start/end anchor.
        $patternMatchers = $parser->parse('/moose/{dog}{/with-a-(cat)-has-a-lot-to-offer}', ['cat' => $stringMatcher->reveal(), 'dog' => $stringMatcher->reveal()], []);
        $this->assertEquals('#\/moose\/(?P<dog>\S+)\/with-a-(?P<cat>\S+)-has-a-lot-to-offer#', $patternMatchers, 'on: /moose/{dog}{/with-a-(cat)-has-a-lot-to-offer}');

        // Test simple and complex parameters where the complex parameter has suffixed text in the {}, with start/end anchor.
        $patternMatchers = $parser->parse('^/moose/{dog}{/with-a-(cat)-has-a-lot-to-offer}$', ['cat' => $stringMatcher->reveal(), 'dog' => $stringMatcher->reveal()], []);
        $this->assertEquals('#^\/moose\/(?P<dog>\S+)\/with-a-(?P<cat>\S+)-has-a-lot-to-offer$#', $patternMatchers, 'on: ^/moose/{dog}{/with-a-(cat)-has-a-lot-to-offer}$');

        // Test simple parameter, with a complex optional parameter where the complex parameter has suffixed text in the {}. Without start/end anchor.
        $patternMatchers = $parser->parse('/moose/{dog}{/with-a-(cat)-has-a-lot-to-offer}', ['dog' => $stringMatcher->reveal()], ['cat' => $stringMatcher->reveal()]);
        $this->assertEquals('#\/moose\/(?P<dog>\S+)(\/with-a-(?P<cat>\S+)-has-a-lot-to-offer)?#', $patternMatchers, 'on (with optional dog param): ^/moose/{dog}{/with-a-(cat)-has-a-lot-to-offer}$');

        // Test simple parameter, with a complex optional parameter where the complex parameter has suffixed text in the {}. With start/end anchor.
        $patternMatchers = $parser->parse('^/moose/{dog}{/with-a-(cat)-has-a-lot-to-offer}$', ['cat' => $stringMatcher->reveal()], ['dog' => $stringMatcher->reveal()]);
        $this->assertEquals('#^\/moose\/((?P<dog>\S+))?\/with-a-(?P<cat>\S+)-has-a-lot-to-offer$#', $patternMatchers, 'on (with optional dog param): ^/moose/{dog}{/with-a-(cat)-has-a-lot-to-offer}$');
    }

}
