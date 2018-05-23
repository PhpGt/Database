<?php
namespace Gt\Database\Connection;

class Driver {
	/** @var SettingsInterface */
	protected $settings;
	/** @var Connection */
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
		$options = [
			Connection::ATTR_ERRMODE => Connection::ERRMODE_EXCEPTION,
		];

		$this->connection = new Connection(
			$this->settings->getConnectionString(),
			$this->settings->getUsername(),
			$this->settings->getPassword(),
			$options
		);
	}
}