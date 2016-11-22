<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Settings;
use Gt\Database\Connection\Driver;
use Gt\Database\Test\Helper;

class QueryCollectionFactoryTest extends \PHPUnit_Framework_TestCase {

public function testCurrentWorkingDirectoryDefault() {
	$queryCollectionName = "exampleTest";
	$baseDir = Helper::getTmpDir();
	$queryCollectionDirectoryPath = "$baseDir/$queryCollectionName";

	mkdir($queryCollectionDirectoryPath, 0775, true);
	chdir($baseDir);

	$driver = new Driver(new Settings($baseDir,
		Settings::DRIVER_SQLITE, Settings::DATABASE_IN_MEMORY));

	$queryCollectionFactory = new QueryCollectionFactory($driver);
	$queryCollection = $queryCollectionFactory->create(
		$queryCollectionName,
		$driver
	);

	$this->assertEquals(
		$queryCollectionDirectoryPath,
		$queryCollection->getDirectoryPath()
	);
}

}#