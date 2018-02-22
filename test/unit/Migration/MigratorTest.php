<?php
namespace Gt\Database\Test\Migration;

use Gt\Database\Connection\Settings;
use Gt\Database\Migration\MigrationException;
use Gt\Database\Migration\Migrator;
use Gt\Database\Test\Helper\Helper;
use PHPUnit\Framework\TestCase;

class MigratorTest extends TestCase {
	protected static $path;

	public static function getPath():string {
		if(!empty(self::$path)) {
			return self::$path;
		}

		self::$path = implode(DIRECTORY_SEPARATOR, [
			Helper::getTmpDir(),
			"query",
			"_migration",
		]);
		mkdir(self::$path, 0775, true);
		return self::$path;
	}

	public function setUp() {
		self::$path = self::getPath();
	}

	public static function tearDownAfterClass() {
		Helper::recursiveRemove(dirname(dirname(self::getPath())));
	}

	public function testMigrationZeroAtStartWithoutTable() {
		$settings = $this->createSettings();
		$migrator = new Migrator($settings, self::getPath());
		self::assertEquals(0, $migrator->getMigrationCount());
	}

	public function testCheckMigrationTableExists() {
		$settings = $this->createSettings();
		$migrator = new Migrator($settings, self::getPath());
		self::assertFalse($migrator->checkMigrationTableExists());
	}

	public function testCreateMigrationTable() {
		$settings = $this->createSettings();
		$migrator = new Migrator($settings, self::getPath());
		$migrator->createMigrationTable();
		self::assertTrue($migrator->checkMigrationTableExists());
	}

	public function testMigrationZeroAtStartWithTable() {
		$settings = $this->createSettings();
		$migrator = new Migrator($settings, self::getPath());
		$migrator->createMigrationTable();
		self::assertEquals(0, $migrator->getMigrationCount());
	}

	/**
	 * @dataProvider dataMigrationFileList
	 */
	public function testGetMigrationFileList(array $fileList) {
		$settings = $this->createSettings();
		$migrator = new Migrator($settings, self::getPath());
		$actualFileList = $migrator->getMigrationFileList();

		self::assertSameSize($actualFileList, $fileList);
	}

	public function testGetMigrationFileListNotExists() {
		$settings = $this->createSettings();
		$migrator = new Migrator(
			$settings,
			dirname(self::getPath()) . "does-not-exist"
		);
		$this->expectException(MigrationException::class);
		$migrator->getMigrationFileList();
	}

	public function dataMigrationFileList(
		$missingFiles = false,
		$duplicateFiles = false
	):array {
		$fileList = [];

		$migLength = rand(10, 200);
		for($migNum = 1; $migNum <= $migLength; $migNum++) {
			$fileName = str_pad(
				$migNum,
				4,
				"0",
				STR_PAD_LEFT
			);
			$fileName .= "-";
			$fileName .= uniqid();
			$fileName .= ".sql";

			$filePath = implode(DIRECTORY_SEPARATOR, [
				self::getPath(),
				$fileName,
			]);

			touch($filePath);
			$fileList []= $filePath;
		}

		return [
			[$fileList]
		];
	}

	protected function createSettings():Settings {
		return new Settings(
			dirname(dirname(self::getPath())),
			Settings::DRIVER_SQLITE,
			Settings::SCHEMA_IN_MEMORY
		);
	}
}