<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;

class QueryCollectionTest extends \PHPUnit_Framework_TestCase {

public function testQueryCollectionQuery() {
	$query = $this->createMock(Query::class);

	$queryFactory = $this->createMock(QueryFactory::class);

	$queryCollection = new QueryCollection(
		__DIR__,
		new Driver(new DefaultSettings()),
		$queryFactory
	);

	$this->assertInstanceOf(
		Query::class,
		$queryCollection->query("something")
	);
}

}#