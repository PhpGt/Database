<?php
namespace Gt\Database\Connection;

class Settings implements SettingsInterface {

const CHARSET = "utf8";
const COLLATION = "utf8_unicode_ci";

const DRIVER_MYSQL = "mysql";
const DRIVER_POSTGRES = "pgsql";
const DRIVER_SQLITE = "sqlite";
const DRIVER_SQLSERVER = "dblib";

const DATABASE_IN_MEMORY = ":memory:";

/** @var string */
protected $baseDirectory;
/** @var string */
protected $dataSource;
/** @var string */
protected $database;
/** @var string */
protected $host;
/** @var int */
protected $port;
/** @var string */
protected $username;
/** @var string */
protected $password;
/** @var string */
protected $tablePrefix;
/** @var string */
protected $connectionName;
/** @var array */
protected $config = [];

public function __construct(
	string $baseDirectory,
	string $dataSource,
	string $database,
	string $host = DefaultSettings::DEFAULT_HOST,
	int $port = null,
	string $username = DefaultSettings::DEFAULT_USERNAME,
	string $password = DefaultSettings::DEFAULT_PASSWORD,
	string $tablePrefix = "",
	string $connectionName = DefaultSettings::DEFAULT_NAME
) {
	if(is_null($port)) {
		$port = DefaultSettings::DEFAULT_PORT[$dataSource];
	}

	$this->baseDirectory = $baseDirectory;
	$this->dataSource = $dataSource;
	$this->database = $database;
	$this->host = $host;
	$this->port = $port;
	$this->username = $username;
	$this->password = $password;
	$this->tablePrefix = $tablePrefix;
	$this->connectionName = $connectionName;
}

public function setConfig(array $config) {
	$this->config = $config;
}

public function getBaseDirectory():string {
	return $this->baseDirectory;
}

public function getDataSource():string {
	return $this->dataSource;
}

public function getDatabase():string {
	return $this->database;
}

public function getHost():string {
	return $this->host;
}

public function getPort():int {
	return $this->port;
}

public function getUsername():string {
	return $this->username;
}

public function getPassword():string {
	return $this->password;
}

public function getConnectionName():string {
	return $this->connectionName;
}

public function getTablePrefix():string {
	return $this->tablePrefix;
}

public function getConnectionSettings():array {
	$currentSettings = [
		"driver" => $this->getDataSource(),
		"host" => $this->getHost(),
		"database" => $this->getDatabase(),
        "port" => $this->getPort(),
        "username" => $this->getUsername(),
		"password" => $this->getPassword(),
		"charset" => self::CHARSET,
		"collation" => self::COLLATION,
		"prefix" => $this->getTablePrefix(),
	];

	return array_merge(
		DefaultSettings::DEFAULT_CONFIG,
		$currentSettings,
		$this->config
	);
}

}#
