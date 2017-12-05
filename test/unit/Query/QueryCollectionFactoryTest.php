<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Settings;
use Gt\Database\Connection\Driver;
use Gt\Database\Test\Helper\Helper;
use PHPUnit\Framework\TestCase;

class QueryCollectionFactoryTest extends TestCase {
	public function testCurrentWorkingDirectoryDefault() {
		$queryCollectionName = "exampleTest";
		$baseDir = Helper::getTmpDir();
		$queryCollectionDirectoryPath = "$baseDir/$queryCollectionName";

		mkdir($queryCollectionDirectoryPath, 0775, true);
		chdir($baseDir);

		$driver = new Driver(new Settings(
				$baseDir,
				Settings::DRIVER_SQLITE,
				Settings::SCHEMA_IN_MEMORY)
		);

		$queryCollectionFactory = new QueryCollectionFactory($driver);
		$queryCollection = $queryCollectionFactory->create(
			$queryCollectionName,
			$driver
		);

		static::assertEquals(
			$queryCollectionDirectoryPath,
			$queryCollection->getDirectoryPath()
		);
	}
}