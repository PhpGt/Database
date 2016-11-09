<?php
namespace Gt\Database\Query;

class QueryFactoryTest extends \PHPUnit_Framework_TestCase {

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 */
public function testFindQueryFilePathExists(
string $queryName, string $directoryOfQueries) {
	$queryFactory = new QueryFactory($directoryOfQueries);
	$queryFilePath = $queryFactory->findQueryFilePath($queryName);
	$this->assertFileExists($queryFilePath);
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathNotExistsProvider
 * @expectedException \Gt\Database\Query\QueryNotFoundException
 */
public function testFindQueryFilePathNotExists(
string $queryName, string $directoryOfQueries) {
	$queryFactory = new QueryFactory($directoryOfQueries);
	$queryFilePath = $queryFactory->findQueryFilePath($queryName);
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExtensionNotValidProvider
 * @expectedException \Gt\Database\Query\QueryNotFoundException
 */
public function testFindQueryFilePathWithInvalidExtension(
string $queryName, string $directoryOfQueries) {
	$queryFactory = new QueryFactory($directoryOfQueries);
	$queryFilePath = $queryFactory->findQueryFilePath($queryName);
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 */
public function testQueryCreated(
string $queryName, string $directoryOfQueries) {
	$queryFactory = new QueryFactory($directoryOfQueries);
	$query = $queryFactory->create($queryName);
	$this->assertInstanceOf(
		"\Gt\Database\Query\QueryInterface",
		$query
	);
}

/**
 * @expectedException \Gt\Database\Query\FactoryClassImplementationException
 */
public function testIncorrectQueryClass() {
	$queryFactory = new QueryFactory("/tmp", \DateTime::class);
}

}#