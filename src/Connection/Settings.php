<?php
namespace Gt\Database\Connection;

class Settings implements SettingsInterface {
	use ImmutableSettings;

	const CHARSET = "utf8";
	const COLLATION = "utf8_unicode_ci";

	const DRIVER_MYSQL = "mysql";
	const DRIVER_POSTGRES = "pgsql";
	const DRIVER_SQLITE = "sqlite";
	const DRIVER_SQLSERVER = "dblib";

	const SCHEMA_IN_MEMORY = ":memory:";

	/** @var string */
	protected $baseDirectory;
	/** @var string */
	protected $dataSource;
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
	protected $tablePrefix;
	/** @var string */
	protected $connectionName;
	/** @var array */
	protected $config = [];

	public function __construct(
		string $baseDirectory,
		string $dataSource,
		string $schema = null,
		string $host = DefaultSettings::DEFAULT_HOST,
		int $port = null,
		string $username = DefaultSettings::DEFAULT_USERNAME,
		string $password = DefaultSettings::DEFAULT_PASSWORD,
		string $tablePrefix = DefaultSettings::DEFAULT_TABLE_PREFIX,
		string $connectionName = DefaultSettings::DEFAULT_NAME
	) {
		if(is_null($port)) {
			$port = DefaultSettings::DEFAULT_PORT[$dataSource];
		}

		$this->baseDirectory = $baseDirectory;
		$this->dataSource = $dataSource;
		$this->schema = $schema;
		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->tablePrefix = $tablePrefix;
		$this->connectionName = $connectionName;
	}

	public function setConfig(array $config) {
		$this->config = $config;
	}

	public function getBaseDirectory():string {
		return $this->baseDirectory;
	}

	public function getDataSource():string {
		return $this->dataSource;
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

	public function getTablePrefix():string {
		return $this->tablePrefix;
	}

	public function getConnectionSettings():array {
		$currentSettings = [
			"driver" => $this->getDataSource(),
			"host" => $this->getHost(),
			"schema" => $this->getSchema(),
			"port" => $this->getPort(),
			"username" => $this->getUsername(),
			"password" => $this->getPassword(),
			"charset" => self::CHARSET,
			"collation" => self::COLLATION,
			"prefix" => $this->getTablePrefix(),
		];

		return array_merge(
			DefaultSettings::DEFAULT_CONFIG,
			$currentSettings,
			$this->config
		);
	}

	public function getConnectionString():string {
		$source = $this->getDataSource();
		$connectionString = "$source:";

		switch($source) {
		case self::DRIVER_SQLITE:
			$connectionString .= $this->getSchema();
			break;

		default:
			$connectionString .= "host=" . $this->getHost();
			$connectionString .= ";dbname=" . $this->getSchema();
			$connectionString .= ";charset=" . self::CHARSET;
			break;
		}

		return $connectionString;
	}
}