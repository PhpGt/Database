<?php
namespace Gt\Database\Connection;

use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase {
	private $properties;

	public function setUp() {
		$this->properties = [
			"baseDirectory" => "/tmp",
			"driver" => "test-driver",
			"database" => "test-database",
			"host" => "test-host",
			"port" => 1234,
			"username" => "test-username",
			"password" => "test-password",
			"connectionName" => "test-connection",
		];
	}

	public function testPropertiesSet() {
		$settings = new Settings(
			$this->properties["baseDirectory"],
			$this->properties["driver"],
			$this->properties["database"],
			$this->properties["host"],
			$this->properties["port"],
			$this->properties["username"],
			$this->properties["password"],
			$this->properties["connectionName"]
		);

		static::assertEquals($this->properties["baseDirectory"], $settings->getBaseDirectory());
		static::assertEquals($this->properties["driver"], $settings->getDriver());
		static::assertEquals($this->properties["database"], $settings->getSchema());
		static::assertEquals($this->properties["host"], $settings->getHost());
		static::assertEquals($this->properties["port"], $settings->getPort());
		static::assertEquals($this->properties["username"], $settings->getUsername());
		static::assertEquals($this->properties["password"], $settings->getPassword());
		static::assertEquals($this->properties["connectionName"], $settings->getConnectionName());
	}

	public function testDefaultConnectionName() {
		$details = [
			"driver" => "test-driver",
			"database" => "test-database",
			"host" => "test-host",
			"port" => 4321,
			"username" => "test-username",
			"password" => "test-password",
		];

		$settings = new Settings(
			"/tmp",
			$details["driver"],
			$details["database"],
			$details["host"],
			$details["port"],
			$details["username"],
			$details["password"]
		);

		static::assertEquals(DefaultSettings::DEFAULT_NAME, $settings->getConnectionName());
	}

	public function testGetConnectionSettings() {
		$settings = new Settings(
			$this->properties["baseDirectory"],
			$this->properties["driver"],
			$this->properties["database"],
			$this->properties["host"],
			$this->properties["port"],
			$this->properties["username"],
			$this->properties["password"],
			$this->properties["connectionName"]
		);

		$expected = [
			"driver" => $this->properties["driver"],
			"host" => $this->properties["host"],
			"port" => $this->properties["port"],
			"schema" => $this->properties["database"],
			"username" => $this->properties["username"],
			"password" => $this->properties["password"],
			"charset" => Settings::CHARSET,
			"collation" => Settings::COLLATION,
			"options" => DefaultSettings::DEFAULT_CONFIG["options"],
		];

		$actual = $settings->getConnectionSettings();
		static::assertEquals($expected, $actual);
	}

	public function testSetConfig() {
		$settings = new Settings(
			$this->properties["baseDirectory"],
			$this->properties["driver"],
			$this->properties["database"],
			$this->properties["host"],
			$this->properties["port"],
			$this->properties["username"],
			$this->properties["password"],
			$this->properties["connectionName"]
		);

		$expected = [
			"optionA" => true,
		];
		$settings->setConfig([
			"options" => $expected
		]);

		$actual = $settings->getConnectionSettings();
		static::assertArrayHasKey("options", $actual);
		static::assertEquals($expected, $actual["options"]);
	}

	public function testGetConnectionString() {
		$settings = new Settings(
			$this->properties["baseDirectory"],
			$this->properties["driver"],
			$this->properties["database"],
			$this->properties["host"],
			$this->properties["port"],
			$this->properties["username"],
			$this->properties["password"],
			$this->properties["connectionName"]
		);

		$expectedConnectionString = implode("", [
			$this->properties["driver"],
			":host=",
			$this->properties["host"],
			";dbname=",
			$this->properties["database"],
			";charset=",
			Settings::CHARSET,
		]);

		self::assertEquals(
			$expectedConnectionString,
			$settings->getConnectionString()
		);
	}
}