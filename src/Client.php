<?php
namespace Gt\Database;

use PDO;
use ArrayAccess;
use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;
use Gt\Database\Connection\Settings;
use Gt\Database\Connection\SettingsInterface;
use Gt\Database\Query\QueryCollection;
use Gt\Database\Query\QueryCollectionFactory;
use Gt\Database\Result\ResultSet;

/**
 * The Database Client stores the factory for creating QueryCollections, and an
 * associative array of connection settings, allowing for multiple database
 * connections. If only one database connection is required, a name is not
 * required as the default name will be used.
 */
class Client implements ArrayAccess {

/** @var QueryCollectionFactory[] */
private $queryCollectionFactoryArray;
/** @var \Gt\Database\Connection\Driver[] */
private $driverArray;

public function __construct(SettingsInterface...$connectionSettings) {
	if(empty($connectionSettings)) {
		$connectionSettings[DefaultSettings::DEFAULT_NAME]
			= new DefaultSettings();
	}

	$this->storeConnectionDriverFromSettings($connectionSettings);
	$this->storeQueryCollectionFactoryFromSettings($connectionSettings);
}

private function storeConnectionDriverFromSettings(array $settingsArray) {
	foreach ($settingsArray as $settings) {
		$connectionName = $settings->getConnectionName();
		$this->driverArray[$connectionName] = new Driver($settings);
	}
}

private function storeQueryCollectionFactoryFromSettings(array $settingsArray) {
	foreach ($settingsArray as $settings) {
		$connectionName = $settings->getConnectionName();
		$this->queryCollectionFactoryArray[$connectionName] =
			new QueryCollectionFactory($this->driverArray[$connectionName]);
	}
}

/**
 * Synonym for ArrayAccess::offsetGet
 */
public function queryCollection(
	string $queryCollectionName,
	string $connectionName = DefaultSettings::DEFAULT_NAME
):QueryCollection {
	$driver = $this->driverArray[$connectionName];
	return $this->queryCollectionFactoryArray[$connectionName]->create(
		$queryCollectionName,
		$driver
	);
}

public function rawStatement(
	string $query,
	array $bindings = [],
	string $connectionName = DefaultSettings::DEFAULT_NAME
):ResultSet {
	$pdo = $this->getPdo($connectionName);
	$statement = $pdo->prepare($query);
	$statement->execute($bindings);
	return new ResultSet($statement, $pdo->lastInsertId());
}

public function getDriver(
	string $connectionName = DefaultSettings::DEFAULT_NAME
):Driver {
	return $this->driverArray[$connectionName];
}

private function getPdo(string $connectionName):PDO {
	$driver = $this->driverArray[$connectionName];
	$connection = $driver->getConnection();
	$pdo = $connection->getPdo();
	return $pdo;
}

// ArrayAccess ////////////////////////////////////////////////////////////////

public function offsetExists($offset) {
	$connectionName = $this->getFirstConnectionName();
	return $this->queryCollectionFactoryArray[$connectionName]->directoryExists(
		$offset);
}

/**
 * @param  string $offset
 * @return QueryCollection
 */
public function offsetGet($offset) {
	$connectionName = $this->getFirstConnectionName();
	return $this->queryCollection($offset, $connectionName);
}

public function offsetSet($offset, $value) {
	throw new ReadOnlyArrayAccessException(self::class);
}

public function offsetUnset($offset) {
	throw new ReadOnlyArrayAccessException(self::class);
}

private function getFirstConnectionName():string {
	reset($this->driverArray);
	return key($this->driverArray);
}

}#