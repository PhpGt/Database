<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;
use Gt\Database\Result\ResultSet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class QueryCollectionTest extends TestCase {
	/** @var  QueryCollection */
	private $queryCollection;
	/** @var MockObject|Query */
	private $mockQuery;

	public function setUp():void {
		/** @var MockObject|QueryFactory $mockQueryFactory */
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

	public function testQueryCollectionQuery() {
		$placeholderVars = ["nombre" => "hombre"];
		$this->mockQuery
			->expects(static::once())
			->method("execute")
			->with([$placeholderVars]);

		$resultSet = $this->queryCollection->query(
			"something",
			$placeholderVars
		);

		static::assertInstanceOf(
			ResultSet::class,
			$resultSet
		);
	}

	public function testQueryCollectionQueryNoParams() {
		$this->mockQuery->expects(static::once())->method("execute")->with();

		$resultSet = $this->queryCollection->query("something");

		static::assertInstanceOf(
			ResultSet::class,
			$resultSet
		);
	}

	public function testQueryShorthand() {
		$placeholderVars = ["nombre" => "hombre"];
		$this->mockQuery
			->expects(static::once())
			->method("execute")
			->with([$placeholderVars]);

		static::assertInstanceOf(
			ResultSet::class,
			$this->queryCollection->something($placeholderVars)
		);
	}

	public function testQueryShorthandNoParams() {
		$this->mockQuery->expects(static::once())->method("execute")->with();

		static::assertInstanceOf(
			ResultSet::class,
			$this->queryCollection->something()
		);
	}
}