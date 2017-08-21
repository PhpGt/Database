<?php
namespace Gt\Database\Connection;

use PDO;

class DefaultSettings implements SettingsInterface {

const CHARSET = "utf8";
const COLLATION = "utf8_unicode_ci";

const DEFAULT_NAME = "default";
const DEFAULT_DATASOURCE = Settings::DRIVER_SQLITE;
const DEFAULT_DATABASE = Settings::DATABASE_IN_MEMORY;
const DEFAULT_HOST = "localhost";
const DEFAULT_PORT = [
	Settings::DRIVER_MYSQL => 3306,
	Settings::DRIVER_POSTGRES => 5432,
	Settings::DRIVER_SQLSERVER => 1433,
	Settings::DRIVER_SQLITE => 0,
];
const DEFAULT_USERNAME = "admin";
const DEFAULT_PASSWORD = "";

const DEFAULT_CONFIG = [
	"options" => [
		PDO::ATTR_EMULATE_PREPARES => true,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	]
];

const DEFAULT_TABLE_PREFIX = "";

public function getBaseDirectory():string {
	return sys_get_temp_dir();
}

public function getDataSource():string {
	return self::DEFAULT_DATASOURCE;
}

public function getDatabase():string {
	return self::DEFAULT_DATABASE;
}

public function getHost():string {
	return self::DEFAULT_HOST;
}

public function getPort():int {
	return self::DEFAULT_PORT[self::getDataSource()];
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
    // NOTE: It's not possible to test the 'port' values returned by this method
    // because the DefaultSettings can only ever return the DEFAULT_DATASOURCE port
    return array_merge(
        DefaultSettings::DEFAULT_CONFIG,
	    [
            "driver" => $this->getDataSource(),
            "host" => $this->getHost(),
            "port" => $this->getPort(),
            "database" => $this->getDatabase(),
            "username" => $this->getUsername(),
            "password" => $this->getPassword(),
            "charset" => self::CHARSET,
            "collation" => self::COLLATION,
            "prefix" => $this->getTablePrefix(),
        ]);
}
}#
