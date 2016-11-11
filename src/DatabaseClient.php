<?php
namespace Gt\Database;

use Gt\Database\Query\QueryCollectionInterface;
use Gt\Database\Query\QueryCollectionFactory;
use Gt\Database\Connection\Settings;
use Gt\Database\Connection\SettingsInterface;
use Gt\Database\Connection\DefaultSettings;

/**
 * The DatabaseClient stores the factory for creating QueryCollections, and an
 * associative array of connection settings, allowing for multiple database
 * connections. If only one database connection is required, a name is not
 * required as the default name will be used.
 */
class DatabaseClient implements DatabaseClientInterface {

/** @var QueryCollectionFactory */
private $queryCollectionFactory;
/** @var SettingsInterface[] */
private $settingsArray;

public function __construct(
QueryCollectionFactory $queryCollectionFactory = null,
SettingsInterface...$connectionSettings) {
	if(empty($connectionSettings)) {
		$connectionSettings[SettingsInterface::DEFAULT_NAME]
			= new DefaultSettings();
	}

	$this->storeConnectionSettings($connectionSettings);

	if(is_null($queryCollectionFactory)) {
		$queryCollectionFactory = new QueryCollectionFactory();
	}

	$this->queryCollectionFactory = $queryCollectionFactory;
}

private function storeConnectionSettings(array $settingsArray) {
	foreach ($settingsArray as $settings) {
		$connectionName = $settings->getConnectionName();
		$this->settingsArray[$connectionName] = $settings;
	}
}

/**
 * Synonym for ArrayAccess::offsetGet
 */
public function queryCollection(
string $queryCollectionName,
string $connectionName = SettingsInterface::DEFAULT_NAME)
:QueryCollectionInterface {
	$settings = $this->settingsArray[$connectionName];

	return $this->queryCollectionFactory->create(
		$queryCollectionName,
		$settings
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