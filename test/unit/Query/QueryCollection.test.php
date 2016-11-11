<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DefaultSettings;

class QueryCollectionTest extends \PHPUnit_Framework_TestCase {

public function testQueryCollectionQuery() {
	$query = $this->createMock(Query::class);

	$queryFactory = $this->createMock(QueryFactory::class);

	$queryCollection = new QueryCollection(
		__DIR__,
		new DefaultSettings(),
		$queryFactory
	);

	$this->assertInstanceOf(
		QueryInterface::class,
		$queryCollection->query("something")
	);
}

}#