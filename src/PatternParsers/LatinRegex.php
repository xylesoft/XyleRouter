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
	protected function yieldParameterTokens($uriPattern) {

		// Find and process all found parameter tokens e.g /{xxx} or {/zzz-(xxx)}.
		if (preg_match_all('#' . static::TOKENS_EXPRESSION . '#', $uriPattern, $matches)) {
			// a more complex pattern, requiring individual parameter matching

			if (array_key_exists('simplePatterns', $matches) && array_key_exists('complexPatterns', $matches)) {

				$simplePatterns = $this->clearEmptyArrayValues($matches['simplePatterns']);
				$complexPatterns = $this->clearEmptyArrayValues($matches['complexPatterns']);

				foreach ($simplePatterns as $pattern) {
					$leftBracket = '{';
					$rightBracket = '}';
					$leftOffsetValue = 1;
					$rightOffsetValue = -1;

					list($tokenName, $tokenValues) = $this->buildYieldArray($pattern, $leftBracket, $rightBracket, $leftOffsetValue, $rightOffsetValue);
					yield $tokenName => $tokenValues;
				}

				foreach ($complexPatterns as $pattern) {
					$leftBracket = '(';
					$rightBracket = ')';
					$leftOffsetValue = 0;
					$rightOffsetValue = 1;

					list($tokenName, $tokenValues) = $this->buildYieldArray($pattern, $leftBracket, $rightBracket, $leftOffsetValue, $rightOffsetValue);

					yield $tokenName => $tokenValues;
				}
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
		$patternsFound = 0;

		foreach ($this->yieldParameterTokens($pattern) as $tokenName => $token) {

			// Make sure the corresponding where() definition exists for the token.
			$requiredToken = array_key_exists($tokenName, $availableTokens);
			$optionalToken = array_key_exists($tokenName, $optionalAvailableTokens);
			if (!$requiredToken && !$optionalToken) {
				throw new \InvalidArgumentException('->where(\'' . $tokenName . '\', ...) missing from your route definition, for token: `' . $tokenName . '` in pattern: `' . $pattern . '`');
			}

			$patternsFound += 1;

			// Gather the token information together.
			$matcher = ($requiredToken) ? $availableTokens[$tokenName] : $optionalAvailableTokens[$tokenName];
			$interpolation = $matcher->getInterpolationPattern();
			$tokenMatching = sprintf('(?P<%s>%s)', $tokenName, $interpolation);

			// Replace 'token' or '(token)' with REGEX.
			$tokenComplexMatching = str_replace(
				$token['partial'],
				$tokenMatching,
				$token['full']
			);

			// remove { and }
			$tokenComplexMatching = mb_substr($tokenComplexMatching, 1, mb_strlen($tokenComplexMatching, 'UTF-8') - 2, 'UTF-8');

			// Add optional (...)? clause around token?
			if ($optionalToken) {
				$tokenComplexMatching = sprintf('(%s)?', $tokenComplexMatching);
			}

			// Fully replace {token} with (REGEX)
			$pattern = str_replace($token['full'], $tokenComplexMatching, $pattern);
		}

		// switch remaining / to \/ so its compatible with regex.
		if ($patternsFound > 0) {
			$pattern = preg_replace('#(?<!\\\\)/#', '$1\/', $pattern);
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

	/**
	 * Remove empty valued array positions.
	 *
	 * @param array $array
	 * @return array
	 */
	protected function clearEmptyArrayValues(array $array) {

		foreach ($array as $key => $value) {
			if (trim($value) === '' || $value === null) {
				unset($array[$key]);
			}
		}

		return array_values($array);
	}

	/**
	 * Construct a consistent array for usage in the parse() look from a yield call.
	 *
	 * @param string $pattern
	 * @param char $leftBracket
	 * @param char $rightBracket
	 * @param int $leftOffsetValue
	 * @param int $rightOffsetValue
	 * @return \Generator
	 */
	protected function buildYieldArray($pattern, $leftBracket, $rightBracket, $leftOffsetValue, $rightOffsetValue) {

		$leftBracketOffset = mb_stripos($pattern, $leftBracket, null, "UTF-8");
		$rightBracketOffset = mb_stripos($pattern, $rightBracket, null, "UTF-8");

		$tokenName = mb_substr($pattern, $leftBracketOffset + $leftOffsetValue, ($rightBracketOffset - $leftBracketOffset) + $rightOffsetValue, 'UTF-8');
		$tokenKey = str_replace(['(', ')'], '', $tokenName);

		return [$tokenKey, ['full' => $pattern, 'partial' => $tokenName]];
	}
}
