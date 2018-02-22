<?php
namespace Gt\Database\Test\Migration;

use Exception;
use Gt\Database\Connection\Settings;
use Gt\Database\Migration\MigrationException;
use Gt\Database\Migration\MigrationSequenceDuplicateException;
use Gt\Database\Migration\MigrationSequenceMissingException;
use Gt\Database\Migration\Migrator;
use Gt\Database\Test\Helper\Helper;
use PHPUnit\Framework\TestCase;

class MigratorTest extends TestCase {
	public function getPath():string {
		$path = implode(DIRECTORY_SEPARATOR, [
			Helper::getTmpDir(),
			"query",
			"_migration",
		]);
		mkdir($path, 0775, true);
		return $path;
	}

	public function setUp() {
	}

	public function tearDown() {
		Helper::recursiveRemove(dirname(dirname($this->getPath())));
	}

	public function testMigrationZeroAtStartWithoutTable() {
		$path = $this->getPath();
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		self::assertEquals(0, $migrator->getMigrationCount());
	}

	public function testCheckMigrationTableExists() {
		$path = $this->getPath();
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		self::assertFalse($migrator->checkMigrationTableExists());
	}

	public function testCreateMigrationTable() {
		$path = $this->getPath();
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$migrator->createMigrationTable();
		self::assertTrue($migrator->checkMigrationTableExists());
	}

	public function testMigrationZeroAtStartWithTable() {
		$path = $this->getPath();
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$migrator->createMigrationTable();
		self::assertEquals(0, $migrator->getMigrationCount());
	}

	/**
	 * @dataProvider dataMigrationFileList
	 */
	public function testGetMigrationFileList(array $fileList) {
		$path = $this->getPath();
		$this->createFiles($fileList, $path);
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$actualFileList = $migrator->getMigrationFileList();
		self::assertSameSize($fileList, $actualFileList);
	}

	public function testGetMigrationFileListNotExists() {
		$path = $this->getPath();
		$settings = $this->createSettings($path);
		$migrator = new Migrator(
			$settings,
			dirname($path) . "does-not-exist"
		);
		$this->expectException(MigrationException::class);
		$migrator->getMigrationFileList();
	}

	/**
	 * @dataProvider dataMigrationFileList
	 */
	public function testCheckFileListOrder(array $fileList) {
		$path = $this->getPath();
		$this->createFiles($fileList, $path);

		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$actualFileList = $migrator->getMigrationFileList();
		$exception = null;

		try {
			$migrator->checkFileListOrder($actualFileList);
		}
		catch(Exception $exception) {}

		self::assertNull(
			$exception,
			"No exception should be thrown"
		);
	}

	/**
	 * @dataProvider dataMigrationFileListMissing
	 */
	public function testCheckFileListOrderMissing(array $fileList) {
		$path = $this->getPath();
		$this->createFiles($fileList, $path);

		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$actualFileList = $migrator->getMigrationFileList();
		$this->expectException(MigrationSequenceMissingException::class);
		$migrator->checkFileListOrder($actualFileList);
	}

	/**
	 * @dataProvider dataMigrationFileListDuplicate
	 */
	public function testCheckFileListOrderDuplicate(array $fileList) {
		$path = $this->getPath();
		$this->createFiles($fileList, $path);

		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$actualFileList = $migrator->getMigrationFileList();
		$this->expectException(MigrationSequenceDuplicateException::class);
		$migrator->checkFileListOrder($actualFileList);
	}

	public function dataMigrationFileList():array {
		$fileList = $this->generateFileList();
		return [
			[$fileList]
		];
	}

	public function dataMigrationFileListMissing():array {
		$fileList = $this->generateFileList(
			true,
			false
		);
		return [
			[$fileList]
		];
	}

	public function dataMigrationFileListDuplicate():array {
		$fileList = $this->generateFileList(
			false,
			true
		);
		return [
			[$fileList]
		];
	}

	private function generateFileList($missingFiles = false, $duplicateFiles = false) {
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

			$fileList []= $fileName;
		}

		if($missingFiles) {
			$numToRemove = rand(1, $migLength / 10);
			for($i = 0; $i < $numToRemove; $i++) {
				$keyToRemove = array_rand($fileList);
				unset($fileList[$keyToRemove]);
			}
		}

		if($duplicateFiles) {
			$numToDuplicate = rand(1, 10);
			for($i = 0; $i < $numToDuplicate; $i++) {
				$keyToDuplicate = array_rand($fileList);
				$newFilename = $fileList[$keyToDuplicate];
				$newFilename = strtok($newFilename, "-");
				$newFilename .= "-";
				$newFilename .= uniqid();
				$newFilename .= ".sql";
				$fileList []= $newFilename;
			}

			$fileList = array_values($fileList);
			sort($fileList);
		}

		$fileList = array_values($fileList);
		return $fileList;
	}

	protected function createSettings(string $path):Settings {
		return new Settings(
			dirname(dirname($path)),
			Settings::DRIVER_SQLITE,
			Settings::SCHEMA_IN_MEMORY
		);
	}

	protected function createFiles(array $files, string $path):void {
		foreach($files as $filename) {
			$pathName = implode(DIRECTORY_SEPARATOR, [
				$path,
				$filename
			]);

			touch($pathName);
		}
	}
}