<?php
/**
 * This class will be refactored into Gt\Config\Config
 * Repository located at https://github.com/PhpGt/Config
 */
namespace Gt\Database\Migration;

use ArrayAccess;

class Config implements ArrayAccess {

private $config = [];

public function __construct(
	string $iniPath,
	array $defaultConfig = [],
	string $section = null
) {
	if(!is_null($section)) {
		$defaultConfigWithSection = [
			$section => $defaultConfig
		];
		$defaultConfig = $defaultConfigWithSection;
	}

	$iniConfig = $this->loadIniConfig($iniPath);
	$iniConfigWithDefaults = array_merge_recursive($defaultConfig, $iniConfig);
	$envConfig = $this->loadEnvConfig($iniConfigWithDefaults);
	$config = array_merge_recursive($iniConfigWithDefaults, $envConfig);

	$this->storeConfig($config, $section);
}

public function loadIniConfig(string $iniPath):array {
	if(!file_exists($iniPath)) {
		return [];
	}

	$config = parse_ini_file($iniPath, true);
	return $config;
}

public function loadEnvConfig(array $mergeWith = []):array {
	$config = [];

	foreach($mergeWith as $section => $store) {
		$config[$section] = [];

		foreach($store as $key => $value) {
			$envVariableName = "{$section}_{$key}";
			$configValue
				= getenv($envVariableName, true) ?: getenv($envVariableName);

			if(false !== $configValue) {
				$config[$section][$key] = $configValue;
			}
		}
	}

	return $config;
}

public function getConfigArray():array {
	return $this->config;
}

private function storeConfig(array $config, string $section = null) {
	$this->config = [];

	if(is_null($section)) {
		$this->config = $config;
	}
	else {
		$this->config = $config[$section];
	}
}

// ArrayAccess /////////////////////////////////////////////////////////////////

public function offsetExists($offset):bool {
	return isset($this->config[$offset]);
}
public function offsetGet($offset) {
	return $this->config[$offset];
}
public function offsetSet($offset, $value) {
	$this->config[$offset] = $value;
}
public function offsetUnset($offset) {
	unset($this->config[$offset]);
}

}#