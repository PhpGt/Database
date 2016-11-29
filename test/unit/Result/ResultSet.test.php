<?php
namespace Gt\Database\Test;

use PDOStatement;
use Gt\Database\Result\ResultSet;

class ResultSetTest extends \PHPUnit_Framework_TestCase {

const FAKE_DATA = [
	["id" => 1, "name" => "Alice"],
	["id" => 2, "name" => "Bob"],
	["id" => 3, "name" => "Charlie"],
];

public function testLengthAndCount() {
	$resultSet = new ResultSet($this->getStatementMock());
	$this->assertEquals(3, $resultSet->length);
	$this->assertEquals(3, $resultSet->getLength());
	$this->assertCount(3, $resultSet);
}

public function testFirstRowArrayAccess() {
	$resultSet = new ResultSet($this->getStatementMock());
	$firstRow = self::FAKE_DATA[0];
	$this->assertEquals($firstRow["id"], $resultSet["id"]);
	$this->assertEquals($firstRow["name"], $resultSet["name"]);
}

public function testIteration() {
	$resultSet = new ResultSet($this->getStatementMock());
	$iterationCount = 0;

	foreach ($resultSet as $row) {
		$fakeRow = self::FAKE_DATA[$iterationCount];

		foreach($row as $key => $value) {
			$this->assertEquals($value, $fakeRow[$key]);
		}

		$iterationCount ++;
	}

	$this->assertEquals(3, $iterationCount);
}

public function testLastInsertId() {
	$resultSet = new ResultSet($this->getStatementMock(), "123");
	$this->assertEquals(123, $resultSet->lastInsertId);
	$this->assertEquals(123, $resultSet->getLastInsertId());
}

private function getStatementMock():PDOStatement {
	$statement = $this->createMock(PDOStatement::class);
	$statement->method("fetch")
	->will(
		$this->onConsecutiveCalls(
			self::FAKE_DATA[0],
			self::FAKE_DATA[1],
			self::FAKE_DATA[2]
		)
	);

	$statement->method("fetchAll")
		->willReturn(self::FAKE_DATA);

	return $statement;
}

}#