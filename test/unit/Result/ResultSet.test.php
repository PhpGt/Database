<?php
namespace Gt\Database\Test;

use Gt\Database\Result\EmptyResultSetException;
use Gt\Database\Result\Row;
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
	$this->assertEquals($firstRow["id"], $resultSet->id);
	$this->assertEquals($firstRow["name"], $resultSet->name);
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

public function testCountTwice() {
	$resultSet = new ResultSet($this->getStatementMock());
	$count = count($resultSet);
	$this->assertCount($count, $resultSet);
}

public function testAccessByRowIndex() {
	$resultSet = new ResultSet($this->getStatementMock());
	self::FAKE_DATA[1]["name"];
	$this->assertEquals(self::FAKE_DATA[1]["name"], $resultSet[1]->name);
	$this->assertEquals(self::FAKE_DATA[0]["name"], $resultSet[0]->name);
	$this->assertEquals(self::FAKE_DATA[2]["name"], $resultSet[2]->name);
}

public function testNoRows() {
    $statement = $this->createMock(PDOStatement::class);
    $statement->method("fetch")->willReturn([]);
    $statement->method("fetchAll")->willReturn([]);
    $resultSet = new ResultSet($statement);

    $this->assertNull($resultSet->fetch());

    $this->expectException(EmptyResultSetException::class);
    $resultSet->binky;
}

public function testFetchAll() {
	$resultSet = new ResultSet($this->getStatementMock());
	$rows = $resultSet->fetchAll();

	foreach(self::FAKE_DATA as $index => $fakeValue) {
		$this->assertArrayHasKey($index, $rows);
		$row = $rows[$index];
		$this->assertInstanceOf(Row::class, $row);
		$this->assertEquals($fakeValue["id"], $row->id);
		$this->assertEquals($fakeValue["name"], $row->name);
	}
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
