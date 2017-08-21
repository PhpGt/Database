<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Driver;
use Gt\Database\Connection\DefaultSettings;

class QueryTest extends \PHPUnit_Framework_TestCase {

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathNotExistsProvider
 * @expectedException QueryNotFoundException
 */
public function testConstructionQueryPathNotExists(
	string $queryName,
	string $queryCollectionPath,
	string $queryPath
) {
	$query = new SqlQuery($queryPath, new Driver(new DefaultSettings()));
}
/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 */
public function testConstructionQueryPathExists(
	string $queryName,
	string $queryCollectionPath,
	string $queryPath
) {
	try {
		$query = new SqlQuery($queryPath, new Driver(new DefaultSettings()));
		$this->assertFileExists($query->getFilePath());
	}
	catch(\Exception $e) {

	}
}

}#