<?php
namespace Gt\Database\Test;

use Gt\Database\Result\EmptyResultSetException;
use Gt\Database\Result\Row;
use PDOStatement;
use Gt\Database\Result\ResultSet;
use PHPUnit\Framework\TestCase;

class ResultSetTest extends TestCase {

const FAKE_DATA = [
	["id" => 1, "name" => "Alice"],
	["id" => 2, "name" => "Bob"],
	["id" => 3, "name" => "Charlie"],
];

public function testLengthAndCount() {
	$resultSet = new ResultSet($this->getStatementMock());
	self::assertEquals(3, $resultSet->length);
	self::assertEquals(3, $resultSet->getLength());
	self::assertCount(3, $resultSet);
}

public function testFirstRowArrayAccess() {
	$resultSet = new ResultSet($this->getStatementMock());
	$firstRow = self::FAKE_DATA[0];
	self::assertEquals($firstRow["id"], $resultSet->id);
	self::assertEquals($firstRow["name"], $resultSet->name);
}

public function testIteration() {
	$resultSet = new ResultSet($this->getStatementMock());
	$iterationCount = 0;

	foreach ($resultSet as $row) {
		$fakeRow = self::FAKE_DATA[$iterationCount];

		foreach($row as $key => $value) {
			self::assertEquals($value, $fakeRow[$key]);
		}

		$iterationCount ++;
	}

	self::assertEquals(3, $iterationCount);
}

public function testLastInsertId() {
	$resultSet = new ResultSet($this->getStatementMock(), "123");
	self::assertEquals(123, $resultSet->lastInsertId);
	self::assertEquals(123, $resultSet->getLastInsertId());
}

public function testCountTwice() {
	$resultSet = new ResultSet($this->getStatementMock());
	$count = count($resultSet);
	self::assertCount($count, $resultSet);
}

public function testAccessByRowIndex() {
	$resultSet = new ResultSet($this->getStatementMock());
	self::FAKE_DATA[1]["name"];
	self::assertEquals(self::FAKE_DATA[1]["name"], $resultSet[1]->name);
	self::assertEquals(self::FAKE_DATA[0]["name"], $resultSet[0]->name);
	self::assertEquals(self::FAKE_DATA[2]["name"], $resultSet[2]->name);
}

public function testNoRows() {
	$statement = $this->createMock(PDOStatement::class);
	$statement->method("fetch")->willReturn([]);
	$statement->method("fetchAll")->willReturn([]);
	$resultSet = new ResultSet($statement);

	self::assertNull($resultSet->fetch());

	$this->expectException(EmptyResultSetException::class);
	$resultSet->binky;
}

public function testFetchAll() {
	$resultSet = new ResultSet($this->getStatementMock());
	$rows = $resultSet->fetchAll();

	foreach(self::FAKE_DATA as $index => $fakeValue) {
		self::assertArrayHasKey($index, $rows);
		$row = $rows[$index];
		self::assertInstanceOf(Row::class, $row);
		self::assertEquals($fakeValue["id"], $row->id);
		self::assertEquals($fakeValue["name"], $row->name);
	}
}

private function getStatementMock():PDOStatement {
	$statement = $this->createMock(PDOStatement::class);
	$statement->method("fetch")
	->will(
		self::onConsecutiveCalls(
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
