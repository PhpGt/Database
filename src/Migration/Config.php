<?php
/**
 * This class will be refactored into Gt\Config\Config
 * Repository located at https://github.com/PhpGt/Config
 */
namespace Gt\Database\Migration;

use ArrayAccess;

class Config implements ArrayAccess {

protected $config = [];

public function __construct(
	string $iniPath,
	array $defaultConfig = []
) {
	$iniConfig = $this->loadIniConfig($iniPath);
	$iniConfigWithDefaults = $this->mergeArrays($defaultConfig, $iniConfig);
	$envConfig = $this->loadEnvConfig($iniConfigWithDefaults);
	$config = $this->mergeArrays($iniConfigWithDefaults, $envConfig);
	$this->storeConfig($config);
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

			if($configValue === '""') {
				$configValue = "";
			}

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

protected function storeConfig(array $config) {
	$this->config = $config;
}

protected function mergeArrays(array $array1, array $array2):array {
	$merged = $array1;

	foreach($array2 as $key => $value) {
		if(is_array($value)
		&& isset($merged[$key])
		&& is_array($merged[$key])) {
			$merged[$key] = $this->mergeArrays($merged[$key], $value);
		}
		else {
			$merged[$key] = $value;
		}
	}

	return $merged;
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
