<?php

namespace Tests\Route;


use Tests\stubs\DummyRequest;
use Xylesoft\XyleRouter\Route\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{

    public function testRouteInstance() {

        $route = new Route('^/cats$', 'cats', null, null);
        $this->assertEquals('#^/cats$#', $route->getRoutePattern());
        $this->assertEquals('cats', $route->getName());
        $this->assertNull($route->getParent());

        $this->assertInstanceOf('\Xylesoft\XyleRouter\Interfaces\Route\RouteInterface', $route);

        $this->assertInstanceOf('\Xylesoft\XyleRouter\Interfaces\Route\RouteCuttingInterface', $route);
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Interfaces\Route\RouteDefaultsInterface', $route);
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Interfaces\Route\RouteMethodsInterface', $route);
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Interfaces\Route\RouteNamingInterface', $route);
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Interfaces\Route\RouteStoppingInterface', $route);
        $this->assertInstanceOf('\Xylesoft\XyleRouter\Interfaces\Route\RouteWhereInterface', $route);
    }

    public function testRouteCutting() {

        $route = new Route('^/cats', 'cats', null, null);
        $route->cut(true);

        $request = new DummyRequest('/cats/are-cool');

        $this->assertEquals($route, $route->match($request));

        $this->assertEquals('/are-cool', $request->getUrl());
    }

}
