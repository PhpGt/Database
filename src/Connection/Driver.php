<?php
namespace Gt\Database\Connection;

use Gt\Database\Connection\SettingsInterface;
use PDO;

class Driver {

/** @var SettingsInterface */
protected $settings;
/** @var PDO */
protected $connection;

public function __construct(SettingsInterface $settings) {
	$this->settings = $settings;
	$this->connect();
}

public function getBaseDirectory():string {
	return $this->settings->getBaseDirectory();
}

public function getConnectionName():string {
	return $this->settings->getConnectionName();
}

public function getConnection():Connection {
	return $this->connection;
}

protected function connect() {
	$options = null;

	$this->connection = new Connection(
		$this->settings->getConnectionString(),
		$this->settings->getUsername(),
		$this->settings->getPassword(),
		$options
	);
}

}#