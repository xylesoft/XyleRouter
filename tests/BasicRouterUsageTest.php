<?php

use Xylesoft\XyleRouter\Router;
use Tests\stubs\DummyRequest;
use Tests\stubs\TokensCallback;

/**
 * Class TestRouter.
 *
 * Testing the Xylesoft\XyleRouter\Router
 */
class BasicRouterUsageTest extends PHPUnit_Framework_TestCase
{

    private function getRouter() {

        $config = new \Xylesoft\XyleRouter\Configuration\Configurations();
        // define the Route class name
        $config->registerConfiguration('route_class_namespace', '\Xylesoft\XyleRouter\Route', 'xylesoft.xylerouter.classes');
        $config->registerConfiguration('route_group_class_namespace', '\Xylesoft\XyleRouter\Group', 'xylesoft.xylerouter.classes');
        $config->registerConfiguration('header_class_namespace', '\Xylesoft\XyleRouter\Header', 'xylesoft.xylerouter.classes');
        $config->registerConfiguration('pattern_parser_class_namespace', new \Xylesoft\XyleRouter\PatternParsers\LatinRegex(), 'xylesoft.xylerouter.shared-classes');

        $router = new Router($config);
        $router->initialize(__DIR__.'/stubs/routes.php');

        return $router;
    }

    public function testRouterClassLoad()
    {
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Router', $this->getRouter());
    }

    /**
     * @expectedException \RuntimeException
     * @depends testRouterClassLoad
     */
    public function testRouteImplementationCheck()
    {
        $config = new \Xylesoft\XyleRouter\Configuration\Configurations();
        // define the Route class name
        $config->registerConfiguration('route_class_namespace', '\Tests\stubs\FakeRoute', 'xylesoft.xylerouter.classes');
        $config->registerConfiguration('route_group_class_namespace', '\Xylesoft\XyleRouter\Group', 'xylesoft.xylerouter.classes');
        $config->registerConfiguration('header_class_namespace', '\Xylesoft\XyleRouter\Header', 'xylesoft.xylerouter.classes');
        $config->registerConfiguration('pattern_parser_class_namespace', new \Xylesoft\XyleRouter\PatternParsers\LatinRegex(), 'xylesoft.xylerouter.shared-classes');

        $router = new Router($config);
    }

    /**
     * @depends testRouterClassLoad
     */
    public function testDefinition()
    {

        $routes = $this->getRouter()->getRoutes();
//        var_dump($routes);die;
        $this->assertCount(6, $routes);
        $this->assertArrayHasKey('locale', $routes);
        $this->assertArrayHasKey('welcome-page', $routes);
        $this->assertArrayHasKey('index-page', $routes);
        $this->assertArrayHasKey('users-statistic-view', $routes);
        $this->assertArrayHasKey('threads', $routes);
        $this->assertArrayHasKey('admin', $routes);

        // check if group route keys exist
        $routeGroup = $routes['threads']->getRoutes();
        $this->assertArrayHasKey('threads.listing', $routeGroup);
        $this->assertArrayHasKey('threads.item', $routeGroup);

        $routeGroup = $routes['admin']->getRoutes();
        $this->assertArrayHasKey('admin.index', $routeGroup);
        $this->assertArrayHasKey('admin.users', $routeGroup);
        $this->assertArrayHasKey('admin.users-view', $routeGroup);
        $this->assertArrayHasKey('admin.superuser', $routeGroup);

        $routeGroup = $routeGroup['admin.superuser']->getRoutes();
        $this->assertArrayHasKey('admin.superuser.all-users', $routeGroup);


        //        $this->assertEquals('index.page', $routes[0]->getName());
//        $this->assertEquals('#^\/hello\/(?P<category>[^\/]+)(\/(?P<age>\d+))?$#', $routes[0]->getRoutePattern());
//        $this->assertInstanceOf('\Closure', $routes[0]->getHandler());
//        $this->assertEquals(['GET'], $routes[0]->getMethods());
//        $this->assertEquals(['age' => '/(32)'], $routes[0]->getDefaults());
//
//        $this->assertEquals('users.statistic.view', $routes[1]->getName());
//        $this->assertEquals('#^\/users\/(?P<name>[^\/]+)\/statistics\/(?P<statistic>[^\/]+)(\/(?P<sort>[^\/]+))?(\/special-offer-for-(?P<clientsForeName>[^\/]+)-only-today)?$#', $routes[1]->getRoutePattern());
//        $this->assertInstanceOf('\Closure', $routes[1]->getHandler());
//        $this->assertEquals(['GET'], $routes[1]->getMethods());
//        $this->assertEquals(['sort' => '/(id)'], $routes[1]->getDefaults());
    }

    public function testBasicRouteMatching() {

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
//        $this->assertContains('Xylesoft\XyleRouter\Interfaces\RouteInterface', array_values(class_implements($result)), "Valid simple route didn't return RouteInterface");
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
//        $this->assertContains('Xylesoft\XyleRouter\Interfaces\RouteInterface', array_values(class_implements($result)), "Valid parameter route didn't return RouteInterface");
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
