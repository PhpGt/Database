<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;

class QueryFactoryTest extends \PHPUnit_Framework_TestCase {

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 */
public function testFindQueryFilePathExists(
string $queryName, string $directoryOfQueries) {
	$queryFactory = new QueryFactory(
		$directoryOfQueries,
		new Driver(new DefaultSettings())
	);
	$queryFilePath = $queryFactory->findQueryFilePath($queryName);
	$this->assertFileExists($queryFilePath);
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathNotExistsProvider
 * @expectedException \Gt\Database\Query\QueryNotFoundException
 */
public function testFindQueryFilePathNotExists(
string $queryName, string $directoryOfQueries) {
	$queryFactory = new QueryFactory(
		$directoryOfQueries,
		new Driver(new DefaultSettings())
	);
	$queryFilePath = $queryFactory->findQueryFilePath($queryName);
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExtensionNotValidProvider
 * @expectedException \Gt\Database\Query\QueryFileExtensionException
 */
public function testFindQueryFilePathWithInvalidExtension(
string $queryName, string $directoryOfQueries) {
	$queryFactory = new QueryFactory(
		$directoryOfQueries,
		new Driver(new DefaultSettings())
	);
	$queryFilePath = $queryFactory->findQueryFilePath($queryName);
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 * @expectedException \Gt\Database\Connection\ConnectionNotConfiguredException
 */
public function testQueryNotCreatedWhenNoDatabase(
string $queryName, string $directoryOfQueries) {
	$queryFactory = new QueryFactory(
		$directoryOfQueries,
		new Driver(new DefaultSettings())
	);
	$query = $queryFactory->create($queryName);
	$this->assertInstanceOf(
		"\Gt\Database\Query\QueryInterface",
		$query
	);
}

}#