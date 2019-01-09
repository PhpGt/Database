<?php

namespace unit\Result;

use Gt\Database\Result\NoSuchColumnException;
use Gt\Database\Result\Row;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase {
	/** @dataProvider data_getTestRow */
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

	public function testEmpty() {
		$row = new Row(["col1" => "item"]);
		$isEmpty = empty($row->col1);
		self::assertFalse($isEmpty);
		$isEmpty = empty($row->col2);
		self::assertTrue($isEmpty);
	}

	public function testNullCoalesce() {
		$row = new Row(["col1" => "item"]);
		$value = $row->col2 ?? "DEFAULT!";
		self::assertEquals("DEFAULT!", $value);
	}

	public function testContains() {
		$row = new Row(["col1" => "item"]);
		self::assertTrue($row->contains("col1"));
		self::assertFalse($row->contains("col2"));
	}

	/** @dataProvider data_getTestRow */
	public function testIteration(array $data) {
		$row = new Row($data);

		foreach($row as $colName => $value) {
			self::assertEquals($data[$colName], $value);
		}
	}

	public function data_getTestRow():array {
		return [
			[["id" => 1, "name" => "Alice"]],
			[["col1" => "binky", "col2" => "boo", "col3", "dah"]],
		];
	}
}