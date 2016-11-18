<?php
namespace Gt\Database\Test;

use Gt\Database\Connection\Driver;
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
 * @expectedException \Gt\Database\Query\PreparedStatementException
 */
public function testPreparedStatementThrowsException(
string $queryName, string $queryCollectionPath, string $queryPath) {
	file_put_contents($queryPath, "insert blahblah into nothing");
	$query = new SqlQuery($queryPath, $this->getDriver());
	$query->execute();
}

private function getDriver():Driver {
	$settings = new Settings(
		Settings::DRIVER_SQLITE,
		Settings::DATABASE_IN_MEMORY,
		"localhost",
		"root",
		""
	);
	$driver = new Driver($settings);

	return $driver;
}

}#