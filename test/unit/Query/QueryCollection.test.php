<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;
use Gt\Database\Result\ResultSet;
use PHPUnit_Framework_MockObject_MockObject;

class QueryCollectionTest extends \PHPUnit_Framework_TestCase {
/** @var  QueryCollection */
private $SUT;
/** @var  PHPUnit_Framework_MockObject_MockObject */
private $mockQuery;

public function testQueryCollectionQuery() {
    $placeholderVars = ["nombre" => "hombre"];
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with($placeholderVars);
    $resultSet = $this->SUT->query("something", $placeholderVars);
    $this->assertInstanceOf(
		ResultSet::class,
        $resultSet
	);
}

public function testQueryCollectionQueryNoParams() {
    $this->mockQuery->expects($this->once())->method("execute")->with();
    $resultSet = $this->SUT->query("something");
    $this->assertInstanceOf(
		ResultSet::class,
        $resultSet
	);
}

public function testQueryShorthand() {
    $placeholderVars = ["nombre" => "hombre"];
    $this->mockQuery
        ->expects($this->once())
        ->method("execute")
        ->with($placeholderVars);

    $this->assertInstanceOf(
        ResultSet::class,
        $this->SUT->something($placeholderVars)
    );
}

public function testQueryShorthandNoParams() {
    $this->mockQuery->expects($this->once())->method("execute")->with();
    $this->assertInstanceOf(
        ResultSet::class,
        $this->SUT->something()
    );
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
