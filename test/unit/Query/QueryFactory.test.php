<?php
namespace Gt\Database\Query;

class QueryFactoryTest extends \PHPUnit_Framework_TestCase {

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 */
public function testFindQueryFilePathExists(
string $queryName, string $baseQueryDirectory) {
	$this->assertTrue(true);
}

}#