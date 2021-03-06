<?php

use Xylesoft\XyleRouter\TokenMatchers\String;
use Xylesoft\XyleRouter\TokenMatchers\Number;

$router
    ->get('^/hello/{category}{/(age)}$', 'index.page')
        ->where(
            'category',
            false,
            new String([
                    'min' => 5,
                ]
            )
        )
        ->where(
            'age',
            true,
            new Number([
                    'min' => 16,
                    'max' => 99,
                ]
            )
        )
        ->defaults([
            'age' => '/(32)',
        ])
        ->handle(function ($parameters, $request) {
            return "Route Matched.";
        })
    ;
