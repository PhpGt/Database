<?php
namespace Gt\Database\Connection;

class DefaultSettings {

const DEFAULT_DATASOURCE = "mysql";
const DEFAULT_DATABASE = "example";
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

}#