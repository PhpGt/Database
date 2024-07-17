<?php
namespace Gt\Database\Connection;

use PDO;

class Driver {
	/** @noinspection PhpUnused */
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

	protected SettingsInterface $settings;
	protected Connection $connection;

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

	protected function connect():void {
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		];

		if($this->settings->getDriver() === Settings::DRIVER_MYSQL) {
			$options[PDO::MYSQL_ATTR_INIT_COMMAND]
				= "SET SESSION collation_connection='"
				. $this->settings->getCollation()
				. "'";
			$options[PDO::MYSQL_ATTR_LOCAL_INFILE] = true;
		}

		$this->connection = new Connection(
			$this->settings->getConnectionString(),
			$this->settings->getUsername(),
			$this->settings->getPassword(),
			$options
		);

		if($initQuery = $this->settings->getInitQuery()) {
			foreach(explode(";", $initQuery) as $q) {
				$this->connection->exec($q);
			}
		}
	}
}
