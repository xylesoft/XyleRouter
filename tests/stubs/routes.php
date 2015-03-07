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

$router
    ->get('^/users/{name}/statistics/{statistic}{/(sort)}{/special-offer-for-(clientsForeName)-only-today}$', 'users.statistic.view')
    ->where('name', false, new String())
    ->where('statistic', false, new String())
    ->where('sort', true, new String())
    ->where('clientsForeName', true, new String())
    ->defaults([
        'sort' => '/(id)',
    ])
    ->handle(function ($parameters, $request) {
        return "Route Matched.";
    })
;



