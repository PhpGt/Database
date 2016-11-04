<?php
namespace Gt\Database;

use Gt\Database\Query\QueryCollection;
use Gt\Database\Query\QueryCollectionFactory;

class DatabaseClientTest extends \PHPUnit_Framework_TestCase {

public function testInterface() {
	$db = new DatabaseClient();
	$this->assertInstanceOf("\Gt\Database\DatabaseClientInterface", $db);
}

public function testQueryCollectionMethod() {
	$argumentMap = [
		["apple", $this->createMock(QueryCollection::class)],
		["orange", $this->createMock(QueryCollection::class)],
	];

	$queryCollectionFactory = $this->createMock(QueryCollectionFactory::class);
	$queryCollectionFactory->method("create")
		->will($this->returnValueMap($argumentMap));

	$db = new DatabaseClient(null, $queryCollectionFactory);

	$this->assertSame($db->queryCollection("apple"), $db["apple"]);
	$this->assertNotSame($db->queryCollection("orange"), $db["apple"]);
}

}#