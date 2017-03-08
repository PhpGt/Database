<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;
use Gt\Database\Result\ResultSet;
use Gt\Database\Result\Row;
use PHPUnit_Framework_MockObject_MockObject;

class QueryCollectionCRUDsTest extends \PHPUnit_Framework_TestCase {
/** @var  QueryCollection */
private $SUT;
/** @var  PHPUnit_Framework_MockObject_MockObject */
private $mockQuery;

// CREATE /////////////////////////////////////////////////////////////////////
public function testCreate() {
    $placeholderVars = ["nombre" => "hombre"];
    $lastInsertID = "1234";
    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("getLastInsertID")
        ->willReturn($lastInsertID);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with($placeholderVars)
        ->willReturn($mockResultSet);

    $this->assertEquals(
        $lastInsertID,
        $this->SUT->create("something", $placeholderVars));
}

public function testCreateNoParams() {
    $lastInsertID = "1234";
    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("getLastInsertID")
        ->willReturn($lastInsertID);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with()
        ->willReturn($mockResultSet);

    $this->assertEquals(
        $lastInsertID,
        $this->SUT->create("something"));
}

// RETRIEVE ///////////////////////////////////////////////////////////////////
public function testRetrieve() {
    $placeholderVars = ["nombre" => "hombre"];
    $expected = new Row(["col1" => "that"]);

    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("fetch")
        ->willReturn($expected);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with($placeholderVars)
        ->willReturn($mockResultSet);

    $actual = $this->SUT->retrieve("something", $placeholderVars);
    $this->assertInstanceOf(Row::class, $actual);
    $this->assertCount(1, $actual);
    $this->assertEquals($expected, $actual);
}

public function testRetrieveNoParams() {
    $expected = new Row(["col1" => "that"]);

    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("fetch")
        ->willReturn($expected);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with()
        ->willReturn($mockResultSet);

    $this->assertEquals(
        $expected,
        $this->SUT->retrieve("something"));
}

public function testRetrieveNoResults() {
    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("fetch")
        ->willReturn(null);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with()
        ->willReturn($mockResultSet);

    $this->assertNull($this->SUT->retrieve("something"));
}

public function testRetrieveAll() {
    $placeholderVars = ["nombre" => "hombre"];
    $expected = [new Row(["col1" => "that"]), new Row(["col1" => "theother"])];

    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("fetchAll")
        ->willReturn($expected);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with($placeholderVars)
        ->willReturn($mockResultSet);

    $actual = $this->SUT->retrieveAll("something", $placeholderVars);
    $this->assertCount(2, $actual);
    $this->assertEquals($expected, $actual);
}

public function testRetrieveAllNoParams() {
    $expected = [new Row(["col1" => "that"]), new Row(["col1" => "theother"])];

    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("fetchAll")
        ->willReturn($expected);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with()
        ->willReturn($mockResultSet);

    $this->assertEquals(
        $expected,
        $this->SUT->retrieveAll("something"));
}

public function testRetrieveAllNoResults() {
    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("fetchAll")
        ->willReturn([]);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with()
        ->willReturn($mockResultSet);

    $actual = $this->SUT->retrieveAll("something");
    $this->assertInternalType("array", $actual);
    $this->assertCount(0, $actual);
}


// UPDATE ///////////////////////////////////////////////////////////////////
public function testUpdate() {
    $placeholderVars = ["nombre" => "hombre"];
    $recordsUpdatedCount = 1;
    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("getAffectedRows")
        ->willReturn($recordsUpdatedCount);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with($placeholderVars)
        ->willReturn($mockResultSet);

    $this->assertEquals(
        $recordsUpdatedCount,
        $this->SUT->update("something", $placeholderVars));
}

public function testUpdateNoParams() {
    $recordsUpdatedCount = 2;
    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("getAffectedRows")
        ->willReturn($recordsUpdatedCount);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with()
        ->willReturn($mockResultSet);

    $this->assertEquals(
        $recordsUpdatedCount,
        $this->SUT->update("something"));
}


// DELETE ///////////////////////////////////////////////////////////////////
public function testDelete() {
    $placeholderVars = ["nombre" => "hombre"];
    $recordsDeletedCount = 0;
    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("getAffectedRows")
        ->willReturn($recordsDeletedCount);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with($placeholderVars)
        ->willReturn($mockResultSet);

    $this->assertEquals(
        $recordsDeletedCount,
        $this->SUT->delete("something", $placeholderVars));
}

public function testDeleteNoParams() {
    $recordsDeletedCount = 2;
    $mockResultSet = $this->createMock(ResultSet::class);
    $mockResultSet
        ->method("getAffectedRows")
        ->willReturn($recordsDeletedCount);
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with()
        ->willReturn($mockResultSet);

    $this->assertEquals(
        $recordsDeletedCount,
        $this->SUT->delete("something"));
}

public function setUp()
{
    $mockQueryFactory = $this->createMock(QueryFactory::class);
    $this->mockQuery = $this->createMock(Query::class);
    $mockQueryFactory
        ->expects($this->once())
        ->method("create")
        ->with("something")
        ->willReturn($this->mockQuery);

    $this->SUT = new QueryCollection(
        __DIR__,
        new Driver(new DefaultSettings()),
        $mockQueryFactory
    );
}
}#
