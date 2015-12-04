<?php

namespace Xylesoft\XyleRouter\PatternParsers;

use Xylesoft\XyleRouter\Interfaces\PatternParserInterface;

/**
 * Class LatinRegex.
 */
class LatinRegex implements PatternParserInterface {

	const TOKENS_EXPRESSION = '(?P<simplePatterns>{([a-zA-Z0-9]+)})|(?P<complexPatterns>{(?:[\/a-z0-9A-Z\-]+)?\(([a-zA-Z0-9]+)\)(?:[\/a-z0-9A-Z\-]+)?})';

	/**
	 * Generator which will return parameter patterns in sequential order as appears in the
	 * path URL route value.
	 *
	 * @param array $simplePatterns		An array of simple patterns such as '../{cat}'
	 * @param array $complexPatterns	An array of complex patterns such as '..{/(cat)}'
	 * @yield ['parameter's token name' => 'fullToken']
	 */
	protected function yieldParameterTokens(array $simplePatterns, array $complexPatterns) {

		for ($i = 0; $i < count($simplePatterns); $i++) {
			$pattern = null;
			if (mb_strlen($simplePatterns[$i]) > 0) {
				$pattern = $simplePatterns[$i];
				$leftBracket = '{';
				$rightBracket = '}';
				$leftOffsetValue = 1;
				$rightOffsetValue = -1;
			} elseif (mb_strlen($complexPatterns[$i]) > 0) {
				$pattern = $complexPatterns[$i];
				$leftBracket = '(';
				$rightBracket = ')';
				$leftOffsetValue = 0;
				$rightOffsetValue = 1;
			}

			if ($pattern) {
				// get token name
				$leftBracketOffset = mb_stripos($pattern, $leftBracket);
				$rightBracketOffset = mb_stripos($pattern, $rightBracket);
				$tokenName = mb_substr($pattern, $leftBracketOffset + $leftOffsetValue, ($rightBracketOffset - $leftBracketOffset) + $rightOffsetValue);

				yield str_replace(['(', ')'], '', $tokenName) => [
					'full' => $pattern,
					'partial' => $tokenName,
				];
			}
		}
	}

	/**
	 * Parses the route definition parameter tokens into valid REGEX.
	 *
	 * @param string $pattern
	 * @param array $availableTokens
	 * @param array $optionalAvailableTokens
	 *
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	public function parse($pattern, array $availableTokens, array $optionalAvailableTokens) {

		$hasStartAnchor = mb_substr($pattern, 0, 1) === '^';
		$hasEndAnchor = mb_substr($pattern, -1) === '$';

		// Find and process all found parameter tokens e.g /{xxx}.
		if (preg_match_all('#' . static::TOKENS_EXPRESSION . '#', $pattern, $matches)) {
			// a more complex pattern, requiring individual parameter matching

			if (array_key_exists('simplePatterns', $matches) && array_key_exists('complexPatterns', $matches)) {

				foreach ($this->yieldParameterTokens($matches['simplePatterns'], $matches['complexPatterns']) as $tokenName => $token) {

					// Make sure the corresponding where() definition exists for the token.
					if (!array_key_exists($tokenName, $availableTokens)) {
						throw new \InvalidArgumentException('->where() missing from your route definition, for token: `' . $tokenName . '` in pattern: `' . $pattern . '`');
					}

					// Gather the token information together.
					$matcher = $availableTokens[$tokenName];
					$interpolation = $matcher->getInterpolationPattern();
					$tokenMatching = sprintf('(?P<%s>%s)', $tokenName, $interpolation);

					// Replace 'token' or '(token)' with REGEX.
					$tokenComplexMatching = str_replace(
						$token['partial'],
						$tokenMatching,
						$token['full']
					);

					// remove { and }
					$tokenComplexMatching = mb_substr($tokenComplexMatching, 1, mb_strlen($tokenComplexMatching) - 2);

					// Is the token optional?
					if (in_array($tokenName, $optionalAvailableTokens)) {
						$tokenComplexMatching = sprintf('(%s)?', $tokenComplexMatching);
					}

					// Fully replace {token} with (REGEX)
					$pattern = str_replace($token['full'], $tokenComplexMatching, $pattern);
				}

				// switch remaining / to \/ so its compatible with regex.
				$pattern = preg_replace('#([^\\\\])/#', '$1\/', $pattern);
			}
		}

		// add start and end regex symbols
		if ($hasEndAnchor && mb_substr($pattern, -1) !== '$') {
			$pattern .= '$';
		}
		if ($hasStartAnchor && mb_substr($pattern, 0, 1) !== '^') {
			$pattern = '^' . $pattern;
		}

		return '#' . $pattern . '#';
	}
}
