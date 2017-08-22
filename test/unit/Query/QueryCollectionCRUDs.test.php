<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;
use Gt\Database\Result\ResultSet;
use Gt\Database\Result\Row;
use PHPUnit_Framework_MockObject_MockObject;

class QueryCollectionCRUDsTest extends \PHPUnit_Framework_TestCase {
/** @var  QueryCollection */
private $queryCollection;
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
		->expects(static::once())
		->method("execute")
		->with($placeholderVars)
		->willReturn($mockResultSet);

	static::assertEquals(
		$lastInsertID,
		$this->queryCollection->create("something", $placeholderVars));
}

public function testCreateNoParams() {
	$lastInsertID = "1234";
	$mockResultSet = $this->createMock(ResultSet::class);
	$mockResultSet
		->method("getLastInsertID")
		->willReturn($lastInsertID);
	$this->mockQuery
		->expects(static::once())
		->method("execute")
		->with()
		->willReturn($mockResultSet);

	static::assertEquals(
		$lastInsertID,
		$this->queryCollection->create("something"));
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
		->expects(static::once())
		->method("execute")
		->with($placeholderVars)
		->willReturn($mockResultSet);

	$actual = $this->queryCollection->retrieve("something", $placeholderVars);
	static::assertInstanceOf(Row::class, $actual);
	static::assertCount(1, $actual);
	static::assertEquals($expected, $actual);
}

public function testRetrieveNoParams() {
	$expected = new Row(["col1" => "that"]);

	$mockResultSet = $this->createMock(ResultSet::class);
	$mockResultSet
		->method("fetch")
		->willReturn($expected);
	$this->mockQuery
		->expects(static::once())
		->method("execute")
		->with()
		->willReturn($mockResultSet);

	static::assertEquals(
		$expected,
		$this->queryCollection->retrieve("something"));
}

public function testRetrieveNoResults() {
	$mockResultSet = $this->createMock(ResultSet::class);
	$mockResultSet
		->method("fetch")
		->willReturn(null);
	$this->mockQuery
		->expects(static::once())
		->method("execute")
		->with()
		->willReturn($mockResultSet);

	static::assertNull($this->queryCollection->retrieve("something"));
}

public function testRetrieveAll() {
	$placeholderVars = ["nombre" => "hombre"];
	$expected = [new Row(["col1" => "that"]), new Row(["col1" => "theother"])];

	$mockResultSet = $this->createMock(ResultSet::class);
	$mockResultSet
		->method("fetchAll")
		->willReturn($expected);
	$this->mockQuery
		->expects(static::once())
		->method("execute")
		->with($placeholderVars)
		->willReturn($mockResultSet);

	$actual = $this->queryCollection->retrieveAll("something", $placeholderVars);
	static::assertCount(2, $actual);
	static::assertEquals($expected, $actual);
}

public function testRetrieveAllNoParams() {
	$expected = [new Row(["col1" => "that"]), new Row(["col1" => "theother"])];

	$mockResultSet = $this->createMock(ResultSet::class);
	$mockResultSet
		->method("fetchAll")
		->willReturn($expected);
	$this->mockQuery
		->expects(static::once())
		->method("execute")
		->with()
		->willReturn($mockResultSet);

	static::assertEquals(
		$expected,
		$this->queryCollection->retrieveAll("something"));
}

public function testRetrieveAllNoResults() {
	$mockResultSet = $this->createMock(ResultSet::class);
	$mockResultSet
		->method("fetchAll")
		->willReturn([]);
	$this->mockQuery
		->expects(static::once())
		->method("execute")
		->with()
		->willReturn($mockResultSet);

	$actual = $this->queryCollection->retrieveAll("something");
	static::assertInternalType("array", $actual);
	static::assertCount(0, $actual);
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
		->expects(static::once())
		->method("execute")
		->with($placeholderVars)
		->willReturn($mockResultSet);

	static::assertEquals($recordsUpdatedCount, $this->queryCollection->update("something", $placeholderVars));
}

public function testUpdateNoParams() {
	$recordsUpdatedCount = 2;
	$mockResultSet = $this->createMock(ResultSet::class);
	$mockResultSet
		->method("getAffectedRows")
		->willReturn($recordsUpdatedCount);
	$this->mockQuery
		->expects(static::once())
		->method("execute")
		->with()
		->willReturn($mockResultSet);

	static::assertEquals(
		$recordsUpdatedCount,
		$this->queryCollection->update("something"));
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
		->expects(static::once())
		->method("execute")
		->with($placeholderVars)
		->willReturn($mockResultSet);

	static::assertEquals(
		$recordsDeletedCount,
		$this->queryCollection->delete("something", $placeholderVars));
}

public function testDeleteNoParams() {
	$recordsDeletedCount = 2;
	$mockResultSet = $this->createMock(ResultSet::class);
	$mockResultSet
		->method("getAffectedRows")
		->willReturn($recordsDeletedCount);
	$this->mockQuery
		->expects(static::once())
		->method("execute")
		->with()
		->willReturn($mockResultSet);

	static::assertEquals(
		$recordsDeletedCount,
		$this->queryCollection->delete("something"));
}

public function setUp() {
	$mockQueryFactory = $this->createMock(QueryFactory::class);
	$this->mockQuery = $this->createMock(Query::class);
	$mockQueryFactory
		->expects(static::once())
		->method("create")
		->with("something")
		->willReturn($this->mockQuery);

	$this->queryCollection = new QueryCollection(
		__DIR__,
		new Driver(new DefaultSettings()),
		$mockQueryFactory
	);
}

}#
