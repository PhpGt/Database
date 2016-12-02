<?php
namespace Gt\Database\Connection;

use PDO;

class DefaultSettings implements SettingsInterface {

const CHARSET = "utf8";
const COLLATION = "utf8_unicode_ci";

const DEFAULT_NAME = "default";
const DEFAULT_DATASOURCE = Settings::DRIVER_SQLITE;
const DEFAULT_DATABASE = Settings::DATABASE_IN_MEMORY;
const DEFAULT_HOSTNAME = "localhost";
const DEFAULT_USERNAME = "admin";
const DEFAULT_PASSWORD = "";

const DEFAULT_CONFIG = [
	"options" => [
		PDO::ATTR_EMULATE_PREPARES => true,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	]
];

public function getBaseDirectory():string {
	return sys_get_temp_dir();
}

public function getDataSource():string {
	return self::DEFAULT_DATASOURCE;
}

public function getDatabase():string {
	return self::DEFAULT_DATABASE;
}

public function getHostname():string {
	return self::DEFAULT_HOSTNAME;
}

public function getUsername():string {
	return self::DEFAULT_USERNAME;
}

public function getPassword():string {
	return self::DEFAULT_PASSWORD;
}

public function getConnectionName():string {
	return self::DEFAULT_NAME;
}

public function getTablePrefix():string {
	return "";
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