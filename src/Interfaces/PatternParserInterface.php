<?php

namespace Xylesoft\XyleRouter\Interfaces;

/**
 * Interface PatternParserInterface
 * @package Xylesoft\XyleRouter\Interfaces
 */
interface PatternParserInterface {


    /**
     * Parse a route pattern into a query that can be used to match incoming URLs.
     *
     * @param string $pattern
     * @param array $availableTokens
     * @param array $optionalAvailableTokens
     * @return mixed
     */
    public function parse($pattern, array $availableTokens, array $optionalAvailableTokens);

}