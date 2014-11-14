<?php

use Xylesoft\XyleRouter\Router;
use Tests\stubs\DummyRequest;
use Tests\stubs\TokensCallback;

/**
 * Class TestRouter
 *
 * Testing the Xylesoft\XyleRouter\Router
 */
class BasicRouterUsageTest extends PHPUnit_Framework_TestCase {

    public function testRouterClassLoad() {

        $router = new Router('\Xylesoft\XyleRouter\Route');
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Router', $router);
    }

    /**
     * @expectedException \RuntimeException
     * @depends testRouterClassLoad
     */
    public function testRouteImplementationCheck() {

        $router = new Router('\Tests\stubs\FakeRoute');
    }

    /**
     * @depends testRouterClassLoad
     */
    public function testDefinition() {
        $router = new Router('\Xylesoft\XyleRouter\Route');
        $router->initialize(__DIR__ . '/stubs/routes.php');

        $routes = $router->getRoutes();
        $this->assertCount(1, $routes);
        $this->assertEquals('index.page', $routes[0]->getName());
        $this->assertEquals('#^/hello/(category:[a-zA-Z\-0-9]+){/(age:\d+)}$#', $routes[0]->getRoutePattern());
        $this->assertInstanceOf('\Closure', $routes[0]->getHandler());
        $this->assertEquals(['GET'], $routes[0]->getMethods());
        $this->assertInstanceOf('\Tests\stubs\TokensCallback', $routes[0]->getCallback());
        $this->assertEquals(['age'=>32], $routes[0]->getDefaults());
    }

    /**
     * @depends testDefinition
     */
    public function testMatching() {
        $router = new Router('\Xylesoft\XyleRouter\Route');
        $router->initialize(__DIR__ . '/stubs/routes.php');

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
        $this->assertNotFalse($result);
        $this->assertArrayHasKey('Xylesoft\XyleRouter\Interfaces\RouteInterface', array_values(class_implements($result)), "Valid simple route didn't return RouteInterface");

        // Validate route with optional parameter
        $result = $router->dispatch(
            new DummyRequest('/goodbye/cats/5')
        );
        $this->assertNotFalse($result);
        $this->assertArrayHasKey('Xylesoft\XyleRouter\Interfaces\RouteInterface', array_values(class_implements($result)), "Valid parameter route didn't return RouteInterface");
    }
}
