<?php
namespace Gt\Database\Test;

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
private $fake_data_index = 0;

public function testCount() {
	$resultSet = new ResultSet($this->getStatementMock());
	self::assertCount(3, $resultSet);
}

public function testFirstRowArrayAccess() {
	$resultSet = new ResultSet($this->getStatementMock());
	$firstRow = self::FAKE_DATA[0];
	$row = $resultSet->fetch();
	self::assertEquals($firstRow["id"], $row->id);
	self::assertEquals($firstRow["name"], $row->name);
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
	self::assertEquals(123, $resultSet->lastInsertId());
}

public function testCountTwice() {
	$resultSet = new ResultSet($this->getStatementMock());
	$count = count($resultSet);
	self::assertCount($count, $resultSet);
}

public function testAccessByRowIndex() {
	$resultSet = new ResultSet($this->getStatementMock());
	self::FAKE_DATA[1]["name"];

	$rowList = $resultSet->fetchAll();

	self::assertEquals(self::FAKE_DATA[1]["name"], $rowList[1]->name);
	self::assertEquals(self::FAKE_DATA[0]["name"], $rowList[0]->name);
	self::assertEquals(self::FAKE_DATA[2]["name"], $rowList[2]->name);
}

public function testNoRows() {
	$statement = $this->createMock(PDOStatement::class);
	$statement->method("fetch")->willReturn(null);
	$resultSet = new ResultSet($statement);

	self::assertNull($resultSet->fetch());
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
	->will(self::returnCallback([$this, "getNextFakeData"]));

	return $statement;
}

public function getNextFakeData() {
	if($this->fake_data_index > count(self::FAKE_DATA)) {
		$this->fake_data_index = 0;
	}
	$data = self::FAKE_DATA[$this->fake_data_index] ?? null;
	$this->fake_data_index ++;
	return $data;

}

}#
