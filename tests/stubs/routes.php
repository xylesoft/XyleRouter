<?php


$router
    ->route('^/hello/(category:[a-zA-Z\-0-9]+){/(age:\d+)}$')
        ->methods(['GET'])
        ->name('index.page')
        ->callback(new \Tests\stubs\TokensCallback)
        ->defaults(['age'=>32])
        ->handle(function($parameters, $request) {
            return "Route Matched.";
        })
    ;
