<?php
namespace Gt\Database\Connection;

class Settings implements SettingsInterface {

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

public function __construct(string $dataSource, string $database,
string $hostname, string $username, string $password) {
	$this->dataSource = $dataSource;
	$this->database = $database;
	$this->hostname = $hostname;
	$this->username = $username;
	$this->password = $password;
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

}#