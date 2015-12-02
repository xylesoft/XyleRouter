<?php

namespace Xylesoft\XyleRouter\Configuration;

/**
 * Class Configurations
 *
 * @package Xylesoft\XyleRouter\Configuration
 */
class Configurations {

	const ROOT_NAMESPACE = 'xylesoft.xylerouter.root';

	/**
	 * @var array Router configurations:
	 * @property string route_class_namespace           The Route class, used in method: route()
	 * @property string route_group_class_namespace     The Route Grouping class, used in method: group()
	 * @property string header_class_namespace          The Route Header class, used in method: header()
	 * @property string pattern_parser_class_namespace  The default Pattern Parser
	 */
	protected $configuration = [
		self::ROOT_NAMESPACE => []
	];

	/**
	 * @param string $config
	 * @param mixed $value
	 * @param string|null $namespace
	 */
	public function registerConfiguration($config, $value, $namespace = null) {

		if ($namespace !== null && is_string($namespace)) {
			if (! array_key_exists($namespace, $this->configuration)) {
				$this->configuration[$namespace] = [];
			}
		} else {
			$namespace = self::ROOT_NAMESPACE;
		}

		$this->configuration[$namespace][$config] = $value;
	}

	/**
	 * @param string $configName
	 * @return mixed
	 */
	public function getConfiguration($configName, $namespace = null) {

		if (! $namespace) {
			$namespace = self::ROOT_NAMESPACE;
		}

		return ($configName === '*') ? $this->configuration[$namespace] : $this->configuration[$namespace][$configName];
	}
}