<?php
namespace Gt\Database\Connection;

use PDO;

class Driver implements DriverInterface {

/** @var \Gt\Database\Connection\SettingsInterface */
private $settings;
/** @var \Gt\Database\Connection\ConnectionInterface */
private $connection;
/** @var \PDO */
private $pdo;

public function __construct(SettingsInterface $settings) {
	$this->settings = $settings;
}

public function connect() {
	$pdo = $this->pdoSingleton();
	$database = $this->settings->getDatabase();
	$tablePrefix = $this->settings->getTablePrefix();
	$config = [];

	$this->connection = new Connection(
		$pdo,
		$database,
		$tablePrefix,
		$config
	);
}

public function getConnection():ConnectionInterface {
	if(is_null($this->connection)) {
		throw new NoConnectionException();
	}

	return $this->connection;
}

private function pdoSingleton():PDO {
	if(is_null($this->pdo)) {
		$this->pdo = new PDO(
			$this->getDsn(),
			$this->settings->getUsername(),
			$this->settings->getPassword(),
			$this->getOptions()
		);
	}

	return $this->pdo;
}

private function getDsn():string {
	$driverName = $this->settings->getDataSource();
	$parameters = implode(";", [
		"dbname=" . $this->settings->getDatabase(),
		"host=" . $this->settings->getHostname(),
	]);

	return implode(":", [
		$driverName,
		$parameters,
	]);
}

private function getOptions():array {
	return [];
}

}#