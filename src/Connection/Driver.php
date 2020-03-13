<?php
namespace Gt\Database\Connection;

class Driver {
	const AVAILABLE_DRIVERS = [
		"cubrid",
		"dblib", // Sybase databases
		"sybase",
		"firebird",
		"ibm",
		"informix",
		"mysql",
		"sqlsrv", // MS SQL Server and SQL Azure databases
		"oci", // Oracle
		"odbc",
		"pgsql", // PostgreSQL
		"sqlite",
		"4D",
	];

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

		if($this->settings->getSchema() === Settings::DRIVER_MYSQL) {
			$options[Connection::MYSQL_ATTR_INIT_COMMAND]
				= "SET SESSION collation_connection='"
				. $this->settings->getCollation()
				. "'";
		}

		$this->connection = new Connection(
			$this->settings->getConnectionString(),
			$this->settings->getUsername(),
			$this->settings->getPassword(),
			$options
		);
	}
}