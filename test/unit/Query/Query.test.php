<?php
namespace Gt\Database\Query;

use Gt\Database\Test\Helper;

class QueryTest extends \PHPUnit_Framework_TestCase {

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 */
public function testConstructionQueryPathExists(
string $queryName, string $queryCollectionPath, string $queryPath) {
	$query = new Query();
}

}#