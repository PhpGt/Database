<?php
namespace Gt\Database\Test\Query;

use Gt\Database\Connection\Settings;
use Gt\Database\Connection\Driver;
use Gt\Database\Query\QueryCollectionFactory;
use Gt\Database\Query\QueryCollectionNotFoundException;
use Gt\Database\Test\Helper\Helper;
use PHPUnit\Framework\TestCase;

class QueryCollectionFactoryTest extends TestCase {
	public function testCurrentWorkingDirectoryDefault() {
		$queryCollectionName = "exampleTest";
		$baseDir = Helper::getTmpDir();
		$queryCollectionDirectoryPath = implode(DIRECTORY_SEPARATOR, [
			$baseDir,
			$queryCollectionName,
		]);

		mkdir($queryCollectionDirectoryPath, 0775, true);
		chdir($baseDir);

		$driver = new Driver(new Settings(
				$baseDir,
				Settings::DRIVER_SQLITE,
				Settings::SCHEMA_IN_MEMORY)
		);

		$queryCollectionFactory = new QueryCollectionFactory($driver);
		$queryCollection = $queryCollectionFactory->create(
			$queryCollectionName
		);

		static::assertEquals(
			$queryCollectionDirectoryPath,
			$queryCollection->getDirectoryPath()
		);
	}

	public function testDirectoryNotExists() {
		$queryCollectionName = "exampleTest";
		$baseDir = Helper::getTmpDir();
		mkdir($baseDir, 0775, true);

		chdir($baseDir);

		$driver = new Driver(new Settings(
			$baseDir,
			Settings::DRIVER_SQLITE,
			Settings::SCHEMA_IN_MEMORY,
		));
		$queryCollectionFactory = new QueryCollectionFactory($driver);

		self::expectException(QueryCollectionNotFoundException::class);
		$queryCollectionFactory->create($queryCollectionName);
	}
}