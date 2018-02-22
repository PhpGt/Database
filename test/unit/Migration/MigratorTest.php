<?php
namespace Gt\Database\Test\Migration;

use Gt\Database\Connection\Settings;
use Gt\Database\Migration\Migrator;
use Gt\Database\Test\Helper\Helper;
use PHPUnit\Framework\TestCase;

class MigratorTest extends TestCase {
	protected $path;

	public function setUp() {
		$this->path = implode(DIRECTORY_SEPARATOR, [
			Helper::getTmpDir(),
			"query",
			"_migration",
		]);
		mkdir($this->path, 0775, true);
	}

	public function tearDown() {
		Helper::recursiveRemove(dirname(dirname($this->path)));
	}

	public function testMigrationZeroAtStartNoTable() {
		$settings = $this->createSettings();
		$migrator = new Migrator($settings, $this->path);
		self::assertEquals(0, $migrator->getMigrationCount());
	}

	public function testCheckMigrationTableExists() {
		$settings = $this->createSettings();
		$migrator = new Migrator($settings, $this->path);
		self::assertFalse($migrator->checkMigrationTableExists());
	}

	public function testCreateMigrationTable() {
		$settings = $this->createSettings();
		$migrator = new Migrator($settings, $this->path);
		$migrator->createMigrationTable();
		self::assertTrue($migrator->checkMigrationTableExists());
	}

	protected function createSettings():Settings {
		return new Settings(
			Helper::getTmpDir(),
			Settings::DRIVER_SQLITE,
			Settings::SCHEMA_IN_MEMORY
		);
	}
}