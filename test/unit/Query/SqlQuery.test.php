<?php
namespace Gt\Database\Test;

use Gt\Database\Connection\Driver;
use Gt\Database\Connection\DriverInterface;
use Gt\Database\Connection\Settings;
use Gt\Database\Connection\SettingsInterface;
use Gt\Database\Query\SqlQuery;

class SqlQueryTest extends \PHPUnit_Framework_TestCase {

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathNotExistsProvider
 * @expectedException \Gt\Database\Query\QueryNotFoundException
 */
public function testQueryNotFound(
string $queryName, string $queryCollectionPath, string $queryPath) {
	$query = new SqlQuery($queryPath, $this->getDriver());
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 */
public function testQueryFound(
string $queryName, string $queryCollectionPath, string $queryPath) {
	$query = new SqlQuery($queryPath, $this->getDriver());
	$this->assertFileExists($query->getFilePath());
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 * @expectedException \Gt\Database\Query\PreparedQueryException
 */
public function testQueryPrepares(
string $queryName, string $queryCollectionPath, string $queryPath) {
	$query = new SqlQuery($queryPath, $this->getDriver());
	$query->prepare();
}

private function getDriver():DriverInterface {
	$settings = new Settings(
		SettingsInterface::DRIVER_SQLITE_MEMORY,
		"GtDatabaseTest",
		"localhost",
		"root",
		""
	);
	$driver = new Driver($settings);
	$driver->connect();

	return $driver;
}

}#