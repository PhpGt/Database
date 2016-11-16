<?php
namespace Gt\Database\Connection;

class Settings implements SettingsInterface {

const CHARSET = "utf-8";
const COLLATION = "utf8_unicode_ci";

const DRIVER_MYSQL = "mysql";
const DRIVER_POSTGRES = "pgsql";
const DRIVER_SQLITE = "sqlite";
const DRIVER_SQLSERVER = "dblib";

const DATABASE_IN_MEMORY = ":memory:";

/** @var string */
private $dataSource;
/** @var string */
private $database;
/** @var string */
private $hostname;
/** @var string */
private $username;
/** @var string */
private $password;
/** @var string */
private $tablePrefix;
/** @var string */
private $connectionName;

public function __construct(string $dataSource, string $database,
string $hostname, string $username, string $password, string $tablePrefix = "",
string $connectionName = DefaultSettings::DEFAULT_NAME) {
	$this->dataSource = $dataSource;
	$this->database = $database;
	$this->hostname = $hostname;
	$this->username = $username;
	$this->password = $password;
	$this->tablePrefix = $tablePrefix;
	$this->connectionName = $connectionName;
}

public function getDataSource():string {
	return $this->dataSource;
}

public function getDatabase():string {
	return $this->database;
}

public function getHostname():string {
	return $this->hostname;
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
	return [
		"driver" => $this->getDataSource(),
		"host" => $this->getHostname(),
		"database" => $this->getDatabase(),
		"username" => $this->getUsername(),
		"password" => $this->getPassword(),
		"charset" => self::CHARSET,
		"collation" => self::COLLATION,
		"prefix" => $this->getTablePrefix(),
	];
}

}#