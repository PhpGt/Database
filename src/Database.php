<?php
namespace Gt\Database;

use DateTime;
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
	const TYPE_BOOL = "bool";
	const TYPE_STRING = "string";
	const TYPE_INT = "int";
	const TYPE_FLOAT = "float";
	const TYPE_DATETIME = "datetime";

	const COLLECTION_SEPARATOR_CHARACTERS = [".", "/", "\\"];
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

	public function fetchBool(string $queryName, ...$bindings):?bool {
		return $this->castQuerySingle(
			self::TYPE_BOOL,
			$queryName,
			$bindings
		);
	}

	public function fetchString(string $queryName, ...$bindings):?string {
		return $this->castQuerySingle(
			self::TYPE_STRING,
			$queryName,
			$bindings
		);
	}

	public function fetchInt(string $queryName, ...$bindings):?int {
		return $this->castQuerySingle(
			self::TYPE_INT,
			$queryName,
			$bindings
		);
	}

	public function fetchFloat(string $queryName, ...$bindings):?float {
		return $this->castQuerySingle(
			self::TYPE_FLOAT,
			$queryName,
			$bindings
		);
	}

	public function fetchDateTime(string $queryName, ...$bindings):?DateTime {
		return $this->castQuerySingle(
			self::TYPE_DATETIME,
			$queryName,
			$bindings
		);
	}

	/** @return bool[] */
	public function fetchAllBool(string $queryName, ...$bindings):array {
		return $this->castQueryMultiple(
			self::TYPE_BOOL,
			$queryName,
			$bindings
		);
	}

	/** @return string[] */
	public function fetchAllString(string $queryName, ...$bindings):array {
		return $this->castQueryMultiple(
			self::TYPE_STRING,
			$queryName,
			$bindings
		);
	}

	/** @return int[] */
	public function fetchAllInt(string $queryName, ...$bindings):array {
		return $this->castQueryMultiple(
			self::TYPE_INT,
			$queryName,
			$bindings
		);
	}

	/** @return float[] */
	public function fetchAllFloat(string $queryName, ...$bindings):array {
		return $this->castQueryMultiple(
			self::TYPE_FLOAT,
			$queryName,
			$bindings
		);
	}

	/** @return DateTime[] */
	public function fetchAllDateTime(string $queryName, ...$bindings):array {
		return $this->castQueryMultiple(
			self::TYPE_DATETIME,
			$queryName,
			$bindings
		);
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

	public function query(string $fullQueryPath, ...$bindings):ResultSet {
		foreach(self::COLLECTION_SEPARATOR_CHARACTERS as $char) {
			if(!strstr($fullQueryPath, $char)) {
				continue;
			}

			$queryCollectionName = substr(
				$fullQueryPath,
				0,
				strrpos($fullQueryPath, $char)
			);
			$queryFile = substr(
				$fullQueryPath,
				strrpos($fullQueryPath, $char) + 1
			);

			break;
		}

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

	protected function castQuerySingle(
		string $type,
		string $queryName,
		...$bindings
	) {
		$row = $this->fetch($queryName, ...$bindings);
		if(is_null($row)) {
			return null;
		}

		return $this->castRow($type, $row);
	}

	protected function castQueryMultiple(
		string $type,
		string $queryName,
		...$bindings
	):array {
		$array = [];

		$resultSet = $this->fetchAll($queryName, ...$bindings);
		foreach($resultSet as $row) {
			$array []= $this->castRow($type, $row);
		}

		return $array;
	}

	protected function castRow(string $type, Row $row) {
		$assocArray = $row->toArray();
		reset($assocArray);
		$key = key($assocArray);
		$value = $assocArray[$key];

		switch($type) {
		case self::TYPE_BOOL:
		case "boolean":
			return (bool)$value;

		case self::TYPE_STRING:
			return (string)$value;

		case self::TYPE_INT:
		case "integer":
			return (int)$value;

		case self::TYPE_FLOAT:
			return (float)$value;

		case self::TYPE_DATETIME:
		case "datetime":
			return new DateTime($value);
		}
	}
}