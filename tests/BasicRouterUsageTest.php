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
    public function testRouterClassLoad()
    {
        $router = new Router('\Xylesoft\XyleRouter\Route');
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Router', $router);
    }

    /**
     * @expectedException \RuntimeException
     * @depends testRouterClassLoad
     */
    public function testRouteImplementationCheck()
    {
        $router = new Router('\Tests\stubs\FakeRoute');
    }

    /**
     * @depends testRouterClassLoad
     */
    public function testDefinition()
    {
        $router = new Router('\Xylesoft\XyleRouter\Route');
        $router->initialize(__DIR__.'/stubs/routes.php');

        $routes = $router->getRoutes();
        $this->assertCount(1, $routes);
        $this->assertEquals('index.page', $routes[0]->getName());
        $this->assertEquals('#^\/hello\/(?P<category>[^\/]+)(\/(?P<age>\d+))?$#', $routes[0]->getRoutePattern());
        $this->assertInstanceOf('\Closure', $routes[0]->getHandler());
        $this->assertEquals(['GET'], $routes[0]->getMethods());
        $this->assertEquals(['age' => '/(32)'], $routes[0]->getDefaults());
    }

    /**
     * @depends testDefinition
     */
    public function testMatching()
    {
        $router = new Router('\Xylesoft\XyleRouter\Route');
        $router->initialize(__DIR__.'/stubs/routes.php');

        // Invalid Route
        $result = $router->dispatch(
            new DummyRequest('/hello')
        );
        $this->assertEquals(false, $result, "Invalid route didn't return false.");

        // Invalid Route with parameter
        $result = $router->dispatch(
            new DummyRequest('/goodbye/cats')
        );
        $this->assertEquals(false, $result, "Invalid route with parameter didn't return false");

        // Valid route without optional parameter
        $result = $router->dispatch(
            new DummyRequest('/hello/cats')
        );
        $this->assertFalse($result, 'test failed for {category} string min length of 5 on /hello/cats');
        $result = $router->dispatch(
            new DummyRequest('/hello/kittens')
        );
        $this->assertNotFalse($result, 'Dispatch result for /hello/kittens is false');
        $this->assertContains('Xylesoft\XyleRouter\Interfaces\RouteInterface', array_values(class_implements($result)), "Valid simple route didn't return RouteInterface");
        $this->assertEquals('index.page', $result->getName());

        // Validate route with optional parameter
        $result = $router->dispatch(
            new DummyRequest('/hello/cats/20')
        );
        $this->assertFalse($result, 'test failed for {category} string min length of 5 on /hello/cats/20 with age parameter.');

        $result = $router->dispatch(
            new DummyRequest('/hello/kittens/20')
        );
        $this->assertNotFalse($result, 'Dispatch result for /hello/kittens/20 is false');
        $this->assertContains('Xylesoft\XyleRouter\Interfaces\RouteInterface', array_values(class_implements($result)), "Valid parameter route didn't return RouteInterface");
        $this->assertEquals('index.page', $result->getName());

        // age too high
        $result = $router->dispatch(
            new DummyRequest('/hello/cats/2000')
        );
        $this->assertFalse($result, 'test failed on /hello/cats/2000 with too high age parameter.');

        // Check if parameters are set onto request.
        $req = new DummyRequest('/hello/kittens/20');
        $result = $router->dispatch($req);

        $this->assertEquals('index.page', $result->getName());
        $this->assertArrayHasKey('category', $req->getParameters());
        $this->assertArrayHasKey('age', $req->getParameters());
        $this->assertEquals('kittens', $req->getParameter('category'));
        $this->assertEquals('20', $req->getParameter('age'));

        // Check if default parameter is populated.
        $req = new DummyRequest('/hello/kittens');
        $result = $router->dispatch($req);

        $this->assertEquals('index.page', $result->getName());
        $this->assertArrayHasKey('category', $req->getParameters());
        $this->assertArrayHasKey('age', $req->getParameters());
        $this->assertEquals('kittens', $req->getParameter('category'));
        $this->assertEquals('32', $req->getParameter('age'));

    }
}
