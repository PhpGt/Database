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

	public function __construct(
		string $baseDirectory,
		string $driver,
		string $schema = null,
		string $host = DefaultSettings::DEFAULT_HOST,
		int $port = null,
		string $username = DefaultSettings::DEFAULT_USERNAME,
		string $password = DefaultSettings::DEFAULT_PASSWORD,
		string $connectionName = DefaultSettings::DEFAULT_NAME,
		string $collation = DefaultSettings::DEFAULT_COLLATION,
		string $charset = DefaultSettings::DEFAULT_CHARSET
	) {
		if(is_null($port)) {
			$port = DefaultSettings::DEFAULT_PORT[$driver];
		}

		$this->baseDirectory = $baseDirectory;
		$this->driver = $driver;
		$this->schema = $schema;
		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->connectionName = $connectionName;
		$this->charset = $charset;
		$this->collation = $collation;
	}

	public function setConfig(array $config) {
		$this->config = $config;
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
		$currentSettings = [
			"driver" => $this->getDriver(),
			"host" => $this->getHost(),
			"schema" => $this->getSchema(),
			"port" => $this->getPort(),
			"username" => $this->getUsername(),
			"password" => $this->getPassword(),
			"charset" => self::CHARSET,
			"collation" => self::COLLATION,
		];

		return array_merge(
			DefaultSettings::DEFAULT_CONFIG,
			$currentSettings,
			$this->config
		);
	}

	public function getConnectionString():string {
		$driver = $this->getDriver();
		$connectionString = "$driver:";

		switch($driver) {
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

	public function getCharset():string {
		if(!empty($this->charset)) {
			return $this->charset;
		}

		return $this->getCharsetFromCollation();
	}

	public function getCollation():string {
		return $this->collation;
	}

	protected function getCharsetFromCollation():string {
		return substr(
			$this->collation,
			0,
			strpos($this->collation, "_")
		);
	}
}