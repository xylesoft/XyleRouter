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

        $router = new Router();

        $this->assertInstanceOf('\Xylesoft\XyleRouter\Router', $router);
    }

    public function testDefinition() {
        $router = new Router();
        $router->initialize(__DIR__ . '/stubs/routes.php');

        $routes = $router->getRoutes();
        $this->assertCount(1, $routes);
        $this->assertEquals('index.page', $routes[0]->getName());
        $this->assertEquals('^/hello/(category:[a-zA-Z\-0-9]+){/(age:\d+)}$', $routes[0]->getRoutePattern());
        $this->assertInstanceOf('\Closure', $routes[0]->getHandler());
        $this->assertEquals(['GET'], $routes[0]->getMethods());
        $this->assertInstanceOf('\Tests\stubs\TokensCallback', $routes[0]->getCallback());
        $this->assertEquals(['age'=>32], $routes[0]->getDefaults());
    }

    public function testMatching() {
        $router = new Router();
        $router->initialize(__DIR__ . '/stubs/routes.php');

        // Invalid Route
        $result = $router->dispatch(
            new DummyRequest('/hello')
        );
        $this->assertEquals(false, $result);

        // Invalid Route with parameter
        $result = $router->dispatch(
            new DummyRequest('/goodbye/cats')
        );
        $this->assertEquals(false, $result);

        // Valid route without optional parameter
        $result = $router->dispatch(
            new DummyRequest('/hello/cats')
        );
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Route', $result);

        // Validate route with optional parameter
        $result = $router->dispatch(
            new DummyRequest('/goodbye/cats/5')
        );
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Route', $result);
    }
}