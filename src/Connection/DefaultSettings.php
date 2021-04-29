<?php
namespace Gt\Database\Connection;

use PDO;

class DefaultSettings implements SettingsInterface {
	use ImmutableSettings;

	const CHARSET = "utf8mb4";
	const COLLATION = "utf8mb4_general_ci";

	const DEFAULT_NAME = "default";
	const DEFAULT_DRIVER = Settings::DRIVER_SQLITE;
	const DEFAULT_SCHEMA = Settings::SCHEMA_IN_MEMORY;
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
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_PERSISTENT => true,
		]
	];

	const DEFAULT_CHARSET = "utf8mb4";
	const DEFAULT_COLLATION = "utf8mb4_general_ci";

	/** @var string */
	protected $baseDirectory;
	/** @var string */
	protected $driver;
	/** @var string */
	protected $schema;
	/** @var string */
	protected $host;
	/** @var int */
	protected $port;
	/** @var string */
	protected $username;
	/** @var string */
	protected $password;
	/** @var string */
	protected $connectionName;
	/** @var array */
	protected $config = [];
	/** @var string */
	protected $charset;
	/** @var string */
	protected $collation;

	public function __construct() {
		$this->baseDirectory = sys_get_temp_dir();
		$this->driver = self::DEFAULT_DRIVER;
		$this->schema = self::DEFAULT_SCHEMA;
		$this->host = self::DEFAULT_HOST;
		$this->port = self::DEFAULT_PORT[$this->driver];
		$this->username = self::DEFAULT_USERNAME;
		$this->password = self::DEFAULT_PASSWORD;
		$this->connectionName = self::DEFAULT_NAME;
	}

	public function getBaseDirectory():string {
		return $this->baseDirectory;
	}

	public function getDriver():string {
		return $this->driver;
	}

	public function getSchema():string {
		return $this->schema;
	}

	public function getHost():string {
		return $this->host;
	}

	public function getPort():int {
		return $this->port;
	}

	public function getUsername():string {
		return $this->username;
	}

	public function getPassword():string {
		return $this->password;
	}

	public function getConnectionName():string {
		return $this->connectionName;
	}

	public function getConnectionSettings():array {
// NOTE: It's not possible to test the 'port' values returned by this method
// because the DefaultSettings can only ever return the DEFAULT_DRIVER port
		return array_merge(
			DefaultSettings::DEFAULT_CONFIG,
			[
				"driver" => $this->getDriver(),
				"host" => $this->getHost(),
				"port" => $this->getPort(),
				"database" => $this->getSchema(),
				"username" => $this->getUsername(),
				"password" => $this->getPassword(),
				"charset" => self::CHARSET,
				"collation" => self::COLLATION,
			]
		);
	}

	public function getConnectionString():string {
		return implode(":", [
			$this->getDriver(),
			$this->getSchema(),
		]);
	}

	public function getCharset():string {
		return self::DEFAULT_CHARSET;
	}

	public function getCollation():string {
		return self::DEFAULT_COLLATION;
	}
}
