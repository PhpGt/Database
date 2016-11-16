<?php
namespace Gt\Database\Connection;

use PDO;
use Illuminate\Database\Connection;
use Illuminate\Database\Capsule\Manager as CapsuleManager;

class Driver implements DriverInterface {

/** @var \Gt\Database\Connection\SettingsInterface */
private $settings;
/** @var \Illuminate\Database\Connection */
private $connection;

public function __construct(SettingsInterface $settings) {
	$this->settings = $settings;
	$this->connect();
}

public function getConnection():Connection {
	return $this->connection;
}

private function connect() {
	$capsuleManager = new CapsuleManager();
	$capsuleManager->addConnection($this->settings->getConnectionSettings());
	$this->connection = $capsuleManager->getConnection();
}

}#