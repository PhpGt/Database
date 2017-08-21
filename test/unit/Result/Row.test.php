<?php

namespace unit\Result;

use Gt\Database\Result\NoSuchColumnException;
use Gt\Database\Result\Row;

class RowTest extends \PHPUnit_Framework_TestCase {

/** @dataProvider getTestRowData */
public function testFieldAccess(array $data) {
	$row = new Row($data);

	foreach ($data as $key => $value) {
		$this->assertEquals($data[$key], $row->$key);
	}
}

public function testIsSet() {
	$row = new Row(["col1" => "item"]);
	$this->assertTrue(isset($row->col1));
}

public function testIsNotSet() {
	$row = new Row(["col1" => "item"]);
	$this->assertFalse(isset($row->col2));
}

public function testGetNonExistentProperty() {
	$row = new Row(["col1" => "item"]);
	$this->expectException(NoSuchColumnException::class);
	$row->doink;
}

/** @dataProvider getTestRowData */
public function testCount(array $data) {
	$row = new Row($data);
	$this->assertEquals(count($data), $row->count());
	$this->assertEquals(count($data), count($row));
}

/** @dataProvider getTestRowData */
public function testIteration(array $data) {
	$row = new Row($data);

	foreach ($row as $colName => $value) {
		$this->assertEquals($data[$colName], $value);
	}
}

public function getTestRowData(): array {
	return [
		[["id" => 1, "name" => "Alice"]],
		[["col1" => "binky", "col2" => "boo", "col3", "dah"]],
	];
}

}#