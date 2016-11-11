<?php
namespace Gt\Database\Connection;

class DefaultSettings implements SettingsInterface {

const DEFAULT_DATASOURCE = SettingsInterface::DRIVER_SQLITE;
const DEFAULT_DATABASE = "/tmp/phpgt-default-database.sqlite";
const DEFAULT_HOSTNAME = "localhost";
const DEFAULT_USERNAME = "admin";
const DEFAULT_PASSWORD = "";

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

}#