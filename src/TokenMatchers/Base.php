<?php

namespace Xylesoft\XyleRouter\TokenMatchers;

use Xylesoft\XyleRouter\Interfaces\TokenMatcherInterface;

abstract class Base implements TokenMatcherInterface
{
     /**
      * Default class options.
      */
     protected $options = [];

    /**
     * Constructor.
     *
     * @param array $options The options which should override the default options of the child class (default: [])
     */
    public function __construct(array $options = [])
    {
        // Merge the incoming options with the default child class options.
        $this->options = array_merge($this->options, $options);
    }
}
