<?php

namespace Xylesoft\XyleRouter\PatternParser;


class StandardRegex {

    public function parse($pattern, array $availableTokens, array $optionalAvailableTokens) {

        // Get all {xxx} tokens.
        if (preg_match_all('#({([a-zA-Z0-9]+)})|({\/\(([a-zA-Z0-9]+)\)})#', $pattern, $matches)) {
            // a more complex pattern, requiring individual parameter matching

            if (array_key_exists('0', $matches) && count($matches[0]) > 0) {

                // Deconstruct regex array into [tokenName => fullTokenSymbol, ...]
                for ($i = 1; $i < (count($matches[0]) * 2); $i++) {

                    // The regular expression splits the tokens into {name} = 0 and {/(name)} = 1 match types
                    // in the array.
                    $isComplex = (mb_strlen(trim($matches[$i][1]))>0);

                    $fullToken = (! $isComplex) ? $matches[$i][0] : $matches[$i][1];
                    $i++;
                    $tokenName = (! $isComplex) ? $matches[$i][0] : $matches[$i][1];

                    // Make sure the corresponding where() definition exists for the token.
                    if (! array_key_exists($tokenName, $availableTokens)) {
                        throw new \InvalidArgumentException('URL token is not defined in your where tokens: '.$tokenName);
                    }

                    // Gather the token information together.
                    $matcher = $availableTokens[$tokenName];
                    $interpolation = $matcher->getInterpolationPattern();
                    $tokenMatching = sprintf('(?P<%s>%s)', $tokenName, $interpolation);

                    // Replace 'token' or '(token)' with REGEX.
                    $tokenComplexMatching = str_replace(
                        ($isComplex) ? '('.$tokenName.')' : $tokenName,
                        $tokenMatching,
                        $fullToken
                    );

                    // remove { and }
                    $tokenComplexMatching = mb_substr($tokenComplexMatching, 1, mb_strlen($tokenComplexMatching) - 2);

                    // Is the token optional?
                    if (in_array($tokenName, $optionalAvailableTokens)) {
                        $tokenComplexMatching = sprintf('(%s)?', $tokenComplexMatching);
                    }

                    // Fully replace {token} with (REGEX)
                    $pattern = str_replace($fullToken, $tokenComplexMatching, $pattern);
                }

                // switch remaining / to \/ so its compatible with regex.
                $pattern = preg_replace('#([^\\\\])/#', '$1\/', $pattern);
            }
        }

        // add start and end regex symbols
        if (mb_substr($pattern, -1) !== '$') {
            $pattern .= '$';
        }
        if (mb_substr($pattern, 0, 1) !== '^') {
            $pattern = '^'.$pattern;
        }

        return '#' . $pattern . '#';
    }
} 