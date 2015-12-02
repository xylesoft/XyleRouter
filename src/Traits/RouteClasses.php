<?php

namespace Xylesoft\XyleRouter\Traits;

use Xylesoft\XyleRouter\Configuration\Configurations;
use Xylesoft\XyleRouter\Interfaces\RouteInterface;

trait RouteClasses {

	protected function getRouteClass($routePattern, $name, RouteInterface $parent = null) {

		$routeClass = $this->configurations->getConfiguration('route_class_namespace', 'xylesoft.xylerouter.classes');
		return new $routeClass(
			$routePattern,
			($parent) ? $parent->getName() . '.' . $name : $name,
			$parent,
			$this->configurations->getConfiguration('pattern_parser_class_namespace', 'xylesoft.xylerouter.shared-classes')
		);
	}

	protected function getRouteGroupClass(Configurations $configurations, $routePattern, $name, $callback, RouteInterface $parent = null) {

		$routeClass = $this->configurations->getConfiguration('route_group_class_namespace', 'xylesoft.xylerouter.classes');
		return new $routeClass(
			$configurations,
			$routePattern,
			($parent) ? $parent->getName() . '.' . $name : $name,
			$parent,
			$callback
		);
	}

	//	protected function getRouteHeaderClass($routePattern, $name) {
	//
	//		$routeClass = $this->configurations->getConfiguration('route_header_class_namespace', 'xylesoft.xylerouter.classes');
	//		return new $routeClass(
	//			$routePattern,
	//			$name,
	//			$this->getConfiguration('pattern_parser_class_namespace', 'xylesoft.xylerouter.shared-classes')
	//		);
	//	}
}