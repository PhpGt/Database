<?php

namespace unit\Result;

use Gt\Database\Result\NoSuchColumnException;
use Gt\Database\Result\Row;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase {
	/** @dataProvider getTestRowData */
	public function testFieldAccess(array $data) {
		$row = new Row($data);

		foreach($data as $key => $value) {
			self::assertEquals($data[$key], $row->$key);
		}
	}

	public function testIsSet() {
		$row = new Row(["col1" => "item"]);
		self::assertTrue(isset($row->col1));
	}

	public function testIsNotSet() {
		$row = new Row(["col1" => "item"]);
		self::assertFalse(isset($row->col2));
	}

	public function testGetNonExistentProperty() {
		$row = new Row(["col1" => "item"]);
		self::expectException(NoSuchColumnException::class);
		$row->doink;
	}

	/** @dataProvider getTestRowData */
	public function testIteration(array $data) {
		$row = new Row($data);

		foreach($row as $colName => $value) {
			self::assertEquals($data[$colName], $value);
		}
	}

	public function getTestRowData():array {
		return [
			[["id" => 1, "name" => "Alice"]],
			[["col1" => "binky", "col2" => "boo", "col3", "dah"]],
		];
	}
}