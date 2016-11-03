<?php
namespace Gt\Database;

use Gt\Database\Entity\TableCollection;
use Gt\Database\Entity\TableCollectionFactory;

class ClientTest extends \PHPUnit_Framework_TestCase {

public function testInterface() {
	$db = new Client();
	$this->assertInstanceOf("\Gt\Database\ClientInterface", $db);
}

public function testTableCollectionMethod() {
	$argumentMap = [
		["apple", $this->createMock(TableCollection::class)],
		["orange", $this->createMock(TableCollection::class)],
	];

	$tableCollectionFactory = $this->createMock(TableCollectionFactory::class);
	$tableCollectionFactory->method("create")
		->will($this->returnValueMap($argumentMap));

	$db = new Client(null, $tableCollectionFactory);
	$testTableCollection3 = $db["orange"];

	$this->assertSame($db->tableCollection("apple"), $db["apple"]);
	$this->assertNotSame($db->tableCollection("orange"), $db["apple"]);
}

}#