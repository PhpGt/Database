<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Test\Helper;

class QueryTest extends \PHPUnit_Framework_TestCase {

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathNotExistsProvider
 * @expectedException \Gt\Database\Query\QueryNotFoundException
 */
public function testConstructionQueryPathNotExists(
string $queryName, string $queryCollectionPath, string $queryPath) {
	$query = new SqlQuery($queryPath, new DefaultSettings());
}
/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 */
public function testConstructionQueryPathExists(
string $queryName, string $queryCollectionPath, string $queryPath) {
	$query = new SqlQuery($queryPath, new DefaultSettings());
	$this->assertFileExists($query->getFilePath());
}

}#