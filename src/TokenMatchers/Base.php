<?php

namespace Xylesoft\XyleRouter\TokenMatchers;

use Xylesoft\XyleRouter\Interfaces\TokenMatcherInterface;

abstract class Base implements TokenMatcherInterface
{
    protected $options = [];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }
}
