<?php
namespace Gt\Database\Query;

class QueryCollectionTest extends \PHPUnit_Framework_TestCase {

public function testQueryCollectionQuery() {
	$query = $this->createMock(Query::class);

	$queryFactory = $this->createMock(QueryFactory::class);

	$queryCollection = new QueryCollection(__DIR__, $queryFactory);
	$this->assertInstanceOf(
		QueryInterface::class,
		$queryCollection->query("something")
	);
}

}#