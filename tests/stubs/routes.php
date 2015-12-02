<?php

use Xylesoft\XyleRouter\TokenMatchers\String;
use Xylesoft\XyleRouter\TokenMatchers\Number;
use Xylesoft\XyleRouter\TokenMatchers\Regex;
use Tests\stubs\TokenMatchers\ThreadType;
use Tests\stubs\TokenMatchers\ThreadId;
use Tests\stubs\TokenMatchers\UrlSlug;

// Setting the Request output context.
//$router
//    ->header('accepts', 'application/json')
//        ->handle(function($request) {
//
//            //$request->setResponseContext('json');
//        })
//        ->stop(false)
//    ;

// Detect locale, defaults to 'en'
$router
	->get('^/{locale}', 'locale')
	->where(
		'locale',
		true,
		new Regex(['pattern' => '(en|de|fr)'])
	)
	->defaults([
		'locale' => 'en'
	])
	->handle(function ($parameters, $request) {

		//$request->setLocale($parameters->get('locale'));
	})
	->cut(true)// removes the "/<locale>" string from the URL before ...
	->stop(false) // ... continuing to allow matching against the remainder of the routing table.
;

// Simple route without parameters.
$router
	->get('^/welcome$', 'welcome-page')
	->handle(function ($parameters, $request) {

		return 'Simple Route Matched.';
	});

// Matching route with parameters, but not too complex
$router
	->get('^/hello/{category}{/(age)}$', 'index-page')
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
	});

// Matching more complex pattern.
$router
	->get('^/users/{name}/statistics/{statistic}{/(sort)}{/special-offer-for-(clientsForeName)-only-today}$', 'users-statistic-view')
	->where('name', false, new String())
	->where('statistic', false, new String())
	->where('sort', true, new String())
	->where('clientsForeName', true, new String())
	->defaults([
		'sort' => '/(id)',
	])
	->handle(function ($parameters, $request) {

		return 'Route Matched.';
	});

$router
	->group('^/{thread_type}', 'threads', function ($router) {

		$router
			->get('^$', 'listing')
			->handle(function ($parameters, $request) {
				// Listing Controller
			});
		$router
			->get('^/{slug}-{thread_id}$', 'item')
			->where('slug', false, new UrlSlug())// check if it matches a url slug string
			->where('thread_id', false, new ThreadId())// check if thread is valid.
			->handle(function ($parameters, $request) {
				// Trigger controller for displaying an individual thread.
			});
	})
	->where('thread_type', false, new ThreadType());

$router
	->group('^/admin', 'admin', function ($router) {

		$router
			->get('^$', 'index')
			->handle(function ($parameters, $request) {

				return "admin.index";
			});

		$router
			->get('^/users$', 'users')
			->handle(function ($parameters, $request) {

				return "admin.users";
			});

		$router
			->get('^/users/{username}$', 'users-view')
			->handle(function ($parameters, $request) {

				return "admin.users.view";
			});

		$router
			->group('^/superuser', 'superuser', function($router) {

				$router
					->get('^/all-users', 'all-users')
					->handle(function($parameters, $request) {

						return 'admin.superuser.all-users';
					});
			});
	});