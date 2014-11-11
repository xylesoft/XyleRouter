<?php

namespace Tests\stubs;

use Xylesoft\XyleRouter\Interfaces\MatchInterface;
use Xylesoft\XyleRouter\Interfaces\RequestInterface;

class TokensCallback implements MatchInterface {

    private $allowedTokens = [
        'cats',
        'dogs',
        '1982',
        'mr-biggles'
    ];

    public function match(array $parameters, RequestInterface $request)
    {
        if (array_key_exists('category', $parameters)) {

            return (in_array($parameters['category'], $this->allowedTokens));
        }

        return false;
    }
} 