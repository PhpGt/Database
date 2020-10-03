<?php
namespace Gt\Database\Test\Connection;

use Gt\Database\Connection\Connection;
use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;
use Gt\Database\Connection\Settings;
use PHPUnit\Framework\TestCase;

class DriverTest extends TestCase {
	protected $baseDirectory = "test-base-directory";
	protected $connectionName = "test-connection-name";
	protected $connectionString = "sqlite::memory:";
	protected $username = "test-username";
	protected $password = "test-password";

	public function testGetBaseDirectory() {
		$settings = $this->getSettingsMock();
		$driver = new Driver($settings);
		self::assertEquals(
			$this->baseDirectory,
			$driver->getBaseDirectory()
		);
		self::assertEquals(
			$this->connectionName,
			$driver->getConnectionName()
		);
		self::assertInstanceOf(
			Connection::class,
			$driver->getConnection()
		);
	}

	protected function getSettingsMock():Settings {
		$settings = $this->createMock(Settings::class);
		$settings->method("getBaseDirectory")
			->willReturn($this->baseDirectory);
		$settings->method("getConnectionName")
			->willReturn($this->connectionName);
		$settings->method("getConnectionString")
			->willReturn($this->connectionString);
		$settings->method("getUsername")
			->willReturn($this->username);
		$settings->method("getPassword")
			->willReturn($this->password);

		return $settings;
	}
}