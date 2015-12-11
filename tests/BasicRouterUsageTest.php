<?php

namespace Tests;

use Xylesoft\XyleRouter\Router;
use Tests\stubs\DummyRequest;
use Tests\stubs\TokensCallback;

/**
 * Class TestRouter.
 *
 * Testing the Xylesoft\XyleRouter\Router
 */
class BasicRouterUsageTest extends \PHPUnit_Framework_TestCase {

	private function getRouter() {

		$config = new \Xylesoft\XyleRouter\Configuration\Configurations();
		// define the Route class name
		$config->registerConfiguration('route_class_namespace', '\Xylesoft\XyleRouter\Route\Route', 'xylesoft.xylerouter.classes');
		$config->registerConfiguration('route_group_class_namespace', '\Xylesoft\XyleRouter\Route\Group', 'xylesoft.xylerouter.classes');
		$config->registerConfiguration('header_class_namespace', '\Xylesoft\XyleRouter\Route\Header', 'xylesoft.xylerouter.classes');
		$config->registerConfiguration('pattern_parser_class_namespace', new \Xylesoft\XyleRouter\PatternParsers\LatinRegex(), 'xylesoft.xylerouter.shared-classes');

		$router = new Router($config);
		$router->initialize(__DIR__ . '/stubs/routes.php');

		return $router;
	}

	public function testRouterClassLoad() {

		$this->assertInstanceOf('\Xylesoft\XyleRouter\Router', $this->getRouter());
	}

	/**
	 * @expectedException \RuntimeException
	 * @depends testRouterClassLoad
	 */
	public function testRouteImplementationCheck() {

		$config = new \Xylesoft\XyleRouter\Configuration\Configurations();
		// define the Route class name
		$config->registerConfiguration('route_class_namespace', '\Tests\stubs\FakeRoute', 'xylesoft.xylerouter.classes');
		$config->registerConfiguration('route_group_class_namespace', '\Xylesoft\XyleRouter\Route\Group', 'xylesoft.xylerouter.classes');
		$config->registerConfiguration('header_class_namespace', '\Xylesoft\XyleRouter\Header', 'xylesoft.xylerouter.classes');
		$config->registerConfiguration('pattern_parser_class_namespace', new \Xylesoft\XyleRouter\PatternParsers\LatinRegex(), 'xylesoft.xylerouter.shared-classes');

		new Router($config);
	}

	/**
	 * @depends testRouterClassLoad
	 */
	public function testDefinition() {

		$routes = $this->getRouter()->getRoutes();
		$this->assertCount(6, $routes);
		$this->assertArrayHasKey('locale', $routes);
		$this->assertArrayHasKey('welcome-page', $routes);
		$this->assertArrayHasKey('index-page', $routes);
		$this->assertArrayHasKey('users-statistic-view', $routes);
		$this->assertArrayHasKey('threads', $routes);
		$this->assertArrayHasKey('admin', $routes);

		$this->assertEquals('locale', $routes['locale']->getName());
		$this->assertEquals('welcome-page', $routes['welcome-page']->getName());
		$this->assertEquals('index-page', $routes['index-page']->getName());
		$this->assertEquals('users-statistic-view', $routes['users-statistic-view']->getName());
		$this->assertEquals('threads', $routes['threads']->getName());
		$this->assertEquals('admin', $routes['admin']->getName());

		$this->assertEquals('#^\/((?P<locale>(en|de|fr)))?#', $routes['locale']->getRoutePattern());
		//$this->assertEquals('#^\/welcome$#', $routes['welcome-page']->getRoutePattern());


//		$this->assertEquals('index-page', $routes['index-page']->getName());
//		$this->assertEquals('users-statistic-view', $routes['users-statistic-view']->getName());
//		$this->assertEquals('threads', $routes['threads']->getName());
//		$this->assertEquals('admin', $routes['admin']->getName());

		// check if group route keys exiest
		$this->assertInstanceOf('Xylesoft\XyleRouter\Route\Group', $routes['threads']);
		$routeGroup = $routes['threads']->getRoutes();
		$this->assertArrayHasKey('threads.listing', $routeGroup);
		$this->assertArrayHasKey('threads.item', $routeGroup);
		$this->assertEquals('threads.listing', $routeGroup['threads.listing']->getName());
		$this->assertEquals('threads.item', $routeGroup['threads.item']->getName());

		$this->assertInstanceOf('Xylesoft\XyleRouter\Route\Group', $routes['admin']);
		$routeGroup = $routes['admin']->getRoutes();
		$this->assertArrayHasKey('admin.index', $routeGroup);
		$this->assertArrayHasKey('admin.users', $routeGroup);
		$this->assertArrayHasKey('admin.users-view', $routeGroup);
		$this->assertArrayHasKey('admin.superuser', $routeGroup);
		$this->assertEquals('admin.index', $routeGroup['admin.index']->getName());
		$this->assertEquals('admin.users', $routeGroup['admin.users']->getName());
		$this->assertEquals('admin.users-view', $routeGroup['admin.users-view']->getName());
		$this->assertEquals('admin.superuser', $routeGroup['admin.superuser']->getName());

		$this->assertInstanceOf('Xylesoft\XyleRouter\Route\Group', $routeGroup['admin.superuser']);
		$routeGroup = $routeGroup['admin.superuser']->getRoutes();
		$this->assertArrayHasKey('admin.superuser.all-users', $routeGroup);
		$this->assertEquals('admin.superuser.all-users', $routeGroup['admin.superuser.all-users']->getName());
	}

	public function testBasicRouteMatching() {

		$router = $this->getRouter();

		// Non-existent route test
		$result = $router->dispatch(
			new DummyRequest('/hello')
		);
		$this->assertFalse($result);

		// root level route test.
		$request = new DummyRequest('/welcome');
		$result = $router->dispatch($request);
		$this->assertInstanceOf('\Xylesoft\XyleRouter\Route\Route', $result);
		$this->assertEquals('welcome-page', $result->getName());
		$this->assertEquals('#^/welcome$#', $result->getRoutePattern());
		$this->assertTrue($result->getStop());
		$this->assertFalse($result->getCut());
		$this->assertEquals(['GET'], $result->getMethods());
		$controller = $result->getHandler();
		$this->assertEquals('Simple Route Matched.', $controller([], $request));
	}

	public function testGroupRouteMatching() {

		$router = $this->getRouter();
		// Non-existent route test
		$result = $router->dispatch(
			new DummyRequest('/admin/moose')
		);
		$this->assertFalse($result);

		// First level group test
		$request = new DummyRequest('/admin');
		$result = $router->dispatch($request);
		$this->assertInstanceOf('\Xylesoft\XyleRouter\Route\Route', $result);
		$this->assertEquals('admin.index', $result->getName());
		$this->assertEquals('#^/admin$#', $result->getRoutePattern());
		$this->assertTrue($result->getStop());
		$this->assertFalse($result->getCut());
		$this->assertEquals(['GET'], $result->getMethods());
		$controller = $result->getHandler();
		$this->assertEquals('admin.index route.', $controller([], $request));

		// Second level group test
		$request = new DummyRequest('/admin/superuser/all-users');
		$result = $router->dispatch($request);
		$this->assertInstanceOf('\Xylesoft\XyleRouter\Route\Route', $result);
		$this->assertEquals('admin.superuser.all-users', $result->getName());
		$this->assertEquals('#^/admin/superuser/all-users$#', $result->getRoutePattern());
		$this->assertTrue($result->getStop());
		$this->assertFalse($result->getCut());
		$this->assertEquals(['POST'], $result->getMethods());
		$controller = $result->getHandler();
		$this->assertEquals('admin.superuser.all-users route.', $controller([], $request));
	}

	public function testUrlParametersForBasicRouteMatching() {

		$router = $this->getRouter();
		$request = new DummyRequest('/hello/random-category');
		$result = $router->dispatch($request);
		$this->assertInstanceOf('\Xylesoft\XyleRouter\Route\Route', $result);
		$this->assertEquals('index-page', $result->getName());
		$this->assertEquals('#^\/hello\/(?P<category>[^\/]+)(\/(?P<age>\d+))?$#', $result->getRoutePattern());
		$this->assertTrue($result->getStop());
		$this->assertFalse($result->getCut());
		$this->assertEquals(['GET'], $result->getMethods());
		$controller = $result->getHandler();
		$this->assertEquals('Index Page Route Matched: random-category and age: 32', $controller([], $request));

		$request = new DummyRequest('/hello/17-random-category/89');
		$result = $router->dispatch($request);
		$this->assertInstanceOf('\Xylesoft\XyleRouter\Route\Route', $result);
		$this->assertEquals('index-page', $result->getName());
		$this->assertEquals('#^\/hello\/(?P<category>[^\/]+)(\/(?P<age>\d+))?$#', $result->getRoutePattern());
		$this->assertTrue($result->getStop());
		$this->assertFalse($result->getCut());
		$this->assertEquals(['GET'], $result->getMethods());
		$controller = $result->getHandler();
		$this->assertEquals('Index Page Route Matched: 17-random-category and age: 89', $controller([], $request));

		$request = new DummyRequest('/hello/17-random-category/hello');
		$result = $router->dispatch($request);
		$this->assertFalse($result);
	}

	//    /**
	//     * @depends testDefinition
	//     */
	//    public function testMatching()
	//    {
	//        $router = new Router('\Xylesoft\XyleRouter\Route');
	//        $router->initialize(__DIR__.'/stubs/routes.php');
	//
	//        // Invalid Route
	//        $result = $router->dispatch(
	//            new DummyRequest('/hello')
	//        );
	//        $this->assertEquals(false, $result, "Invalid route didn't return false.");
	//
	//        // Invalid Route with parameter
	//        $result = $router->dispatch(
	//            new DummyRequest('/goodbye/cats')
	//        );
	//        $this->assertEquals(false, $result, "Invalid route with parameter didn't return false");
	//
	//        // Valid route without optional parameter
	//        $result = $router->dispatch(
	//            new DummyRequest('/hello/cats')
	//        );
	//        $this->assertFalse($result, 'test failed for {category} string min length of 5 on /hello/cats');
	//        $result = $router->dispatch(
	//            new DummyRequest('/hello/kittens')
	//        );
	//        $this->assertNotFalse($result, 'Dispatch result for /hello/kittens is false');
	//        $this->assertContains('Xylesoft\XyleRouter\Interfaces\Route\RouteInterface', array_values(class_implements($result)), "Valid simple route didn't return RouteInterface");
	//        $this->assertEquals('index.page', $result->getName());
	//
	//        // Validate route with optional parameter
	//        $result = $router->dispatch(
	//            new DummyRequest('/hello/cats/20')
	//        );
	//        $this->assertFalse($result, 'test failed for {category} string min length of 5 on /hello/cats/20 with age parameter.');
	//
	//        $result = $router->dispatch(
	//            new DummyRequest('/hello/kittens/20')
	//        );
	//        $this->assertNotFalse($result, 'Dispatch result for /hello/kittens/20 is false');
	//        $this->assertContains('Xylesoft\XyleRouter\Interfaces\Route\RouteInterface', array_values(class_implements($result)), "Valid parameter route didn't return RouteInterface");
	//        $this->assertEquals('index.page', $result->getName());
	//
	//        // age too high
	//        $result = $router->dispatch(
	//            new DummyRequest('/hello/cats/2000')
	//        );
	//        $this->assertFalse($result, 'test failed on /hello/cats/2000 with too high age parameter.');
	//
	//        // Check if parameters are set onto request.
	//        $req = new DummyRequest('/hello/kittens/20');
	//        $result = $router->dispatch($req);
	//
	//        $this->assertEquals('index.page', $result->getName());
	//        $this->assertArrayHasKey('category', $req->getParameters());
	//        $this->assertArrayHasKey('age', $req->getParameters());
	//        $this->assertEquals('kittens', $req->getParameter('category'));
	//        $this->assertEquals('20', $req->getParameter('age'));
	//
	//        // Check if default parameter is populated.
	//        $req = new DummyRequest('/hello/kittens');
	//        $result = $router->dispatch($req);
	//
	//        $this->assertEquals('index.page', $result->getName());
	//        $this->assertArrayHasKey('category', $req->getParameters());
	//        $this->assertArrayHasKey('age', $req->getParameters());
	//        $this->assertEquals('kittens', $req->getParameter('category'));
	//        $this->assertEquals('32', $req->getParameter('age'));
	//
	//        // Check if more complex pattern works
	//        $req = new DummyRequest('/users/pancho/statistics/breed/id');
	//        $result = $router->dispatch($req);
	//
	//        $this->assertEquals('users.statistic.view', $result->getName());
	//        $this->assertArrayHasKey('name', $req->getParameters());
	//        $this->assertArrayHasKey('statistic', $req->getParameters());
	//        $this->assertArrayHasKey('sort', $req->getParameters());
	//
	//        $this->assertEquals('pancho', $req->getParameter('name'));
	//        $this->assertEquals('breed', $req->getParameter('statistic'));
	//        $this->assertEquals('id', $req->getParameter('sort'));
	//
	//        // Check if full more complex pattern works
	//        $req = new DummyRequest('/users/pancho/statistics/breed/claw-size/special-offer-for-pancho-only-today');
	//        $result = $router->dispatch($req);
	//
	//        $this->assertEquals('users.statistic.view', $result->getName());
	//        $this->assertArrayHasKey('name', $req->getParameters());
	//        $this->assertArrayHasKey('statistic', $req->getParameters());
	//        $this->assertArrayHasKey('sort', $req->getParameters());
	//        $this->assertArrayHasKey('clientsForeName', $req->getParameters());
	//
	//        $this->assertEquals('pancho', $req->getParameter('name'));
	//        $this->assertEquals('breed', $req->getParameter('statistic'));
	//        $this->assertEquals('claw-size', $req->getParameter('sort'));
	//        $this->assertEquals('pancho', $req->getParameter('clientsForeName'));
	//    }
}
