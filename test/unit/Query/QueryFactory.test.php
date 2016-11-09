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

}#