<?php
namespace Gt\Database;

use Gt\Database\Connection\Connection;
use Gt\Database\Result\Row;
use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;
use Gt\Database\Connection\SettingsInterface;
use Gt\Database\Query\QueryCollection;
use Gt\Database\Query\QueryCollectionFactory;
use Gt\Database\Result\ResultSet;
use PDOException;

/**
 * The Database Client stores the factory for creating QueryCollections, and an
 * associative array of connection settings, allowing for multiple database
 * connections. If only one database connection is required, a name is not
 * required as the default name will be used.
 */
class Database {
	/** @var QueryCollectionFactory[] */
	protected $queryCollectionFactoryArray;
	/** @var Driver[] */
	protected $driverArray;
	/** @var Connection */
	protected $currentConnectionName;

	public function __construct(SettingsInterface...$connectionSettings) {
		if(empty($connectionSettings)) {
			$connectionSettings[DefaultSettings::DEFAULT_NAME]
				= new DefaultSettings();
		}

		$this->storeConnectionDriverFromSettings($connectionSettings);
		$this->storeQueryCollectionFactoryFromSettings($connectionSettings);
	}

	public function fetch(string $queryName, ...$bindings):?Row {
		$result = $this->query($queryName, $bindings);

		return $result->fetch();
	}

	public function fetchAll(string $queryName, ...$bindings):ResultSet {
		return $this->query($queryName, $bindings);
	}

	public function insert(string $queryName, ...$bindings):int {
		$result = $this->query($queryName, $bindings);

		return $result->lastInsertId();
	}

	public function delete(string $queryName, ...$bindings):int {
		$result = $this->query($queryName, $bindings);

		return $result->affectedRows();
	}

	public function update(string $queryName, ...$bindings):int {
		$result = $this->query($queryName, $bindings);

		return $result->affectedRows();
	}

	public function query(string $queryName, ...$bindings):ResultSet {
		$queryCollectionName = substr(
			$queryName,
			0,
			strrpos($queryName, "/")
		);
		$queryFile = substr(
			$queryName,
			strrpos($queryName, "/") + 1
		);

		$connectionName = $this->currentConnectionName ?? DefaultSettings::DEFAULT_NAME;
		$queryCollection = $this->queryCollection(
			$queryCollectionName,
			$connectionName
		);

		return $queryCollection->query($queryFile, $bindings);
	}

	public function setCurrentConnectionName(string $connectionName) {
		$this->currentConnectionName = $this->getNamedConnection(
			$connectionName
		);
	}

	public function executeSql(
		string $query,
		array $bindings = [],
		string $connectionName = DefaultSettings::DEFAULT_NAME
	):ResultSet {
		$connection = $this->getNamedConnection($connectionName);
		try {
			$statement = $connection->prepare($query);
		}
		catch(PDOException $exception) {
			throw new DatabaseException(
				$exception->getMessage(),
				intval($exception->getCode())
			);
		}

		$statement->execute($bindings);

		return new ResultSet($statement, $connection->lastInsertId());
	}

	protected function getNamedConnection(string $connectionName):Connection {
		$driver = $this->driverArray[$connectionName];

		return $driver->getConnection();
	}

	protected function storeConnectionDriverFromSettings(array $settingsArray) {
		foreach($settingsArray as $settings) {
			$connectionName = $settings->getConnectionName();
			$this->driverArray[$connectionName] = new Driver($settings);
		}
	}

	protected function storeQueryCollectionFactoryFromSettings(array $settingsArray) {
		foreach($settingsArray as $settings) {
			$connectionName = $settings->getConnectionName();
			$this->queryCollectionFactoryArray[$connectionName] =
				new QueryCollectionFactory($this->driverArray[$connectionName]);
		}
	}

	public function queryCollection(
		string $queryCollectionName,
		string $connectionName = DefaultSettings::DEFAULT_NAME
	):QueryCollection {
		return $this->queryCollectionFactoryArray[$connectionName]->create($queryCollectionName);
	}

	public function getDriver(
		string $connectionName = DefaultSettings::DEFAULT_NAME
	):Driver {
		return $this->driverArray[$connectionName];
	}

	protected function getFirstConnectionName():string {
		reset($this->driverArray);

		return key($this->driverArray);
	}
}