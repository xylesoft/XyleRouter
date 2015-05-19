<?php

use Xylesoft\XyleRouter\TokenMatchers\String;
use Xylesoft\XyleRouter\TokenMatchers\Number;
use Xylesoft\XyleRouter\TokenMatchers\Regex;

// Setting the Request output context.
$router
    ->header('accepts', 'application/json')
        ->handle(function($request) {

            //$request->setResponseContext('json');
        })
    ;

// Detect locale, defaults to 'en'
$router
    ->get('^/{locale}', 'locale')
        ->where(
            'locale',
            true,
            new Regex(['pattern' => '(en|de|fr)'])
        )
        ->default([
            'locale'=>'en'
        ])
        ->handle(function($parameters, $request) {

//            $request->setLocale($parameters->get('locale'));
        })
        ->cut(true) // removes the "/<locale>" string from the URL before ...
        ->stop(false) // ... continuing to allow matching against the remainder of the routing table.
    ;

// Simple route without parameters.
$router
    ->get('^/welcome$', 'welcome.page')
        ->handle(function($parameters, $request) {
            return 'Simple Route Matched.';
        })
    ;

// Matching route with parameters, but not too complex
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
            return 'Route Matched.';
        })
    ;

// Matching more complex pattern.
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
            return 'Route Matched.';
        })
    ;


$router
    ->group('^/admin', 'admin.')
        ->handle(function($routerGroup) {

            $routerGroup
                ->get('^$', 'index')
                    ->handle(function($parameters, $request) {

                        return "admin.index";
                    })
                ;

            $routerGroup
                ->get('^/users$', 'users')
                    ->handle(function($parameters, $request) {

                        return "admin.users";
                    })
                ;

            $routerGroup
                ->get('^/users/{username}$', 'users.view')
                    ->handle(function($parameters, $request) {

                        return "admin.users.view";
                    })
                ;

        })
    ;
