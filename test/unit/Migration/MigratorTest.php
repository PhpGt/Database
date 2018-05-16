<?php
namespace Gt\Database\Test\Migration;

use Exception;
use Gt\Database\Client;
use Gt\Database\Connection\Settings;
use Gt\Database\Migration\MigrationDirectoryNotFoundException;
use Gt\Database\Migration\MigrationFileNameFormatException;
use Gt\Database\Migration\MigrationIntegrityException;
use Gt\Database\Migration\MigrationSequenceOrderException;
use Gt\Database\Migration\Migrator;
use Gt\Database\Test\Helper\Helper;
use PHPUnit\Framework\TestCase;
use stdClass;

class MigratorTest extends TestCase {
	const MIGRATION_CREATE
		= "create table `test` (`id` int primary key, `name` varchar(32))";
	const MIGRATION_ALTER = "alter table `test` add `new_column` varchar(32)";

	public function getMigrationDirectory():string {
		$tmp = Helper::getTmpDir();

		$path = implode(DIRECTORY_SEPARATOR, [
			$tmp,
			"query",
			"_migration",
		]);
		mkdir($path, 0775, true);
		return $path;
	}

	public function testMigrationZeroAtStartWithoutTable() {
		$path = $this->getMigrationDirectory();
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		self::assertEquals(0, $migrator->getMigrationCount());
	}

	public function testCheckMigrationTableExists() {
		$path = $this->getMigrationDirectory();
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		self::assertFalse($migrator->checkMigrationTableExists());
	}

	public function testCreateMigrationTable() {
		$path = $this->getMigrationDirectory();
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$migrator->createMigrationTable();
		self::assertTrue($migrator->checkMigrationTableExists());
	}

	public function testMigrationZeroAtStartWithTable() {
		$path = $this->getMigrationDirectory();
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$migrator->createMigrationTable();
		self::assertEquals(0, $migrator->getMigrationCount());
	}

	/**
	 * @dataProvider dataMigrationFileList
	 */
	public function testGetMigrationFileList(array $fileList) {
		$path = $this->getMigrationDirectory();
		$this->createFiles($fileList, $path);
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$actualFileList = $migrator->getMigrationFileList();
		self::assertSameSize($fileList, $actualFileList);
	}

	public function testGetMigrationFileListNotExists() {
		$path = $this->getMigrationDirectory();
		$settings = $this->createSettings($path);
		$migrator = new Migrator(
			$settings,
			dirname($path) . "does-not-exist"
		);
		$this->expectException(MigrationDirectoryNotFoundException::class);
		$migrator->getMigrationFileList();
	}

	/**
	 * @dataProvider dataMigrationFileList
	 */
	public function testCheckFileListOrder(array $fileList) {
		$path = $this->getMigrationDirectory();
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
		$path = $this->getMigrationDirectory();
		$this->createFiles($fileList, $path);

		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$actualFileList = $migrator->getMigrationFileList();
		$this->expectException(MigrationSequenceOrderException::class);
		$migrator->checkFileListOrder($actualFileList);
	}

	/**
	 * @dataProvider dataMigrationFileListDuplicate
	 */
	public function testCheckFileListOrderDuplicate(array $fileList) {
		$path = $this->getMigrationDirectory();
		$this->createFiles($fileList, $path);

		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$actualFileList = $migrator->getMigrationFileList();
		$this->expectException(MigrationSequenceOrderException::class);
		$migrator->checkFileListOrder($actualFileList);
	}

	/**
	 * @dataProvider dataMigrationFileList
	 */
	public function testCheckIntegrityGood(array $fileList) {
		$path = $this->getMigrationDirectory();

		$this->createMigrationFiles($fileList, $path);
		$this->hashMigrationToDb($fileList, $path);

		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$absoluteFileList = array_map(function($file)use($path) {
			return implode(DIRECTORY_SEPARATOR, [
				$path,
				$file,
			]);
		},$fileList);

		self::assertEquals(
			count($absoluteFileList),
			$migrator->checkIntegrity($absoluteFileList)
		);
	}

	/**
	 * @dataProvider dataMigrationFileList
	 */
	public function testCheckIntegrityBad(array $fileList) {
		$path = $this->getMigrationDirectory();

		$this->createMigrationFiles($fileList, $path);
		$this->hashMigrationToDb($fileList, $path);

		$migrationToBreak = implode(DIRECTORY_SEPARATOR, [
			$path,
			$fileList[array_rand($fileList)],
		]);
		$sql = file_get_contents($migrationToBreak);
		$sql = substr_replace(
			$sql,
			"EDITED",
			rand(0, 20),
			0
		);
		file_put_contents($migrationToBreak, $sql);

		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$absoluteFileList = array_map(function($file)use($path) {
			return implode(DIRECTORY_SEPARATOR, [
				$path,
				$file,
			]);
		},$fileList);

		self::expectException(MigrationIntegrityException::class);
		$migrator->checkIntegrity($absoluteFileList);
	}

	public function testMigrationCountZeroAtStart() {
		$path = $this->getMigrationDirectory();
		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		self::assertEquals(0, $migrator->getMigrationCount());
	}

	/**
	 * @dataProvider dataMigrationFileList
	 */
	public function testMigrationCountNotZeroAfterMigration(array $fileList) {
		$path = $this->getMigrationDirectory();

		$this->createMigrationFiles($fileList, $path);
		$this->hashMigrationToDb($fileList, $path);

		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$absoluteFileList = array_map(function($file)use($path) {
			return implode(DIRECTORY_SEPARATOR, [
				$path,
				$file,
			]);
		},$fileList);

		self::assertEquals(
			count($absoluteFileList),
			$migrator->getMigrationCount()
		);
	}

	/**
	 * @dataProvider dataMigrationFileList
	 */
	public function testMigrationFileNameFormat(array $fileList) {
		$path = $this->getMigrationDirectory();

		$this->createMigrationFiles($fileList, $path);
		$this->hashMigrationToDb($fileList, $path);

		$settings = $this->createSettings($path);
		$migrator = new Migrator($settings, $path);
		$absoluteFileList = array_map(function($file)use($path) {
			return implode(DIRECTORY_SEPARATOR, [
				$path,
				$file,
			]);
		},$fileList);

		$fileToBreakFormat = array_rand($absoluteFileList);
		$absoluteFileList[$fileToBreakFormat] = str_replace(
			".sql",
			".broken",
			$absoluteFileList[$fileToBreakFormat]
		);

		self::expectException(MigrationFileNameFormatException::class);
		$migrator->checkFileListOrder($absoluteFileList);
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

	protected function createMigrationFiles(array $fileList, string $path):void {
		foreach($fileList as $i => $fileName) {
			$migPathName = implode(DIRECTORY_SEPARATOR, [
				$path,
				$fileName,
			]);
			if($i === 0) {
				$mig = self::MIGRATION_CREATE;
			}
			else {
				$mig = self::MIGRATION_ALTER;
				$mig = str_replace(
					"`new_column`",
					"`new_column_$i`",
					$mig
				);
			}

			file_put_contents($migPathName, $mig);
		}
	}

	protected function hashMigrationToDb(
		array $fileList,
		string $path,
		bool $stopEarly = false
	):void {
		$hashUpTo = null;

		if($stopEarly) {
			$hashUpTo = count($fileList) - rand(0, count($fileList) - 5);
		}

		$settings = $this->createSettings($path);
		$db = new Client($settings);
		$db->executeSql(implode("\n", [
			"create table `_migration` (",
			"`" . Migrator::COLUMN_QUERY_NUMBER . "` int primary key,",
			"`" . Migrator::COLUMN_QUERY_HASH . "` varchar(32) not null,",
			"`" . Migrator::COLUMN_MIGRATED_AT . "` datetime not null )",
		]));

		foreach($fileList as $i => $file) {
			if(!is_null($hashUpTo)
			&& $i >= $hashUpTo) {
				break;
			}

			$migNum = $i + 1;
			$migPathName = implode(DIRECTORY_SEPARATOR, [
				$path,
				$file,
			]);
			$hash = md5_file($migPathName);

			$sql = implode("\n", [
				"insert into `_migration` (",
				"`" . Migrator::COLUMN_QUERY_NUMBER . "`, ",
				"`" . Migrator::COLUMN_QUERY_HASH . "`, ",
				"`" . Migrator::COLUMN_MIGRATED_AT . "` ",
				") values (",
				"?, ?, datetime('now')",
				")",
			]);

			$db->executeSql($sql, [$migNum, $hash]);
		}
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
		$sqlitePath = implode(DIRECTORY_SEPARATOR, [
			dirname($path),
			"migrator-test.db",
		]);
		$sqlitePath = str_replace("\\", "/", $sqlitePath);

		return new Settings(
			dirname(dirname($path)),
			Settings::DRIVER_SQLITE,
			$sqlitePath
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