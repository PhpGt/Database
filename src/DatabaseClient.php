<?php
namespace Gt\Database;

use ArrayAccess;
use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;
use Gt\Database\Connection\Settings;
use Gt\Database\Connection\SettingsInterface;
use Gt\Database\Query\QueryCollection;
use Gt\Database\Query\QueryCollectionFactory;

/**
 * The DatabaseClient stores the factory for creating QueryCollections, and an
 * associative array of connection settings, allowing for multiple database
 * connections. If only one database connection is required, a name is not
 * required as the default name will be used.
 */
class DatabaseClient implements ArrayAccess {

/** @var QueryCollectionFactory */
private $queryCollectionFactory;
/** @var \Gt\Database\Connection\Driver[] */
private $driverArray;

public function __construct(
QueryCollectionFactory $queryCollectionFactory = null,
SettingsInterface...$connectionSettings) {
	if(empty($connectionSettings)) {
		$connectionSettings[DefaultSettings::DEFAULT_NAME]
			= new DefaultSettings();
	}

	$this->storeConnectionDriversFromSettings($connectionSettings);

	if(is_null($queryCollectionFactory)) {
		$queryCollectionFactory = new QueryCollectionFactory();
	}

	$this->queryCollectionFactory = $queryCollectionFactory;
}

private function storeConnectionDriversFromSettings(array $settingsArray) {
	foreach ($settingsArray as $settings) {
		$connectionName = $settings->getConnectionName();
		$this->driverArray[$connectionName] = new Driver($settings);
	}
}

/**
 * Synonym for ArrayAccess::offsetGet
 */
public function queryCollection(
string $queryCollectionName,
string $connectionName = DefaultSettings::DEFAULT_NAME)
:QueryCollection {
	$driver = $this->driverArray[$connectionName];

	return $this->queryCollectionFactory->create(
		$queryCollectionName,
		$driver
	);
}

public function offsetExists($offset) {
	return $this->queryCollectionFactory->directoryExists($offset);
}

public function offsetGet($offset) {
	return $this->queryCollection($offset);
}

public function offsetSet($offset, $value) {
	throw new ReadOnlyArrayAccessException(self::class);
}

public function offsetUnset($offset) {
	throw new ReadOnlyArrayAccessException(self::class);
}

}#