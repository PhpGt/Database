<?php

namespace unit\Result;


use Gt\Database\Result\NoSuchColumnException;
use Gt\Database\Result\Row;

class RowTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider getTestRowData */
    public function testFieldAccess(array $data)
    {
        $SUT = new Row($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($data[$key], $SUT->$key);
        }
    }

    public function testIsSet()
    {
        $SUT = new Row(["col1" => "item"]);
        $this->assertTrue(isset($SUT->col1));
    }

    public function testIsNotSet()
    {
        $SUT = new Row(["col1" => "item"]);
        $this->assertFalse(isset($SUT->col2));
    }

    public function testGetNonExistentProperty()
    {
        $SUT = new Row(["col1" => "item"]);
        $this->expectException(NoSuchColumnException::class);
        $SUT->doink;
    }

    /** @dataProvider getTestRowData */
    public function testCount(array $data)
    {
        $SUT = new Row($data);
        $this->assertEquals(count($data), $SUT->count());
        $this->assertEquals(count($data), count($SUT));
    }

    /** @dataProvider getTestRowData */
    public function testIteration(array $data)
    {
        $SUT = new Row($data);

        foreach ($SUT as $colName => $value) {
            $this->assertEquals($data[$colName], $value);
        }
    }

    public function getTestRowData(): array
    {
        return [
            [["id" => 1, "name" => "Alice"]],
            [["col1" => "binky", "col2" => "boo", "col3", "dah"]],
        ];
    }
}
