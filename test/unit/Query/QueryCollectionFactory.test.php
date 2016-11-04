<?php
namespace Gt\Database\Query;

class QueryCollectionFactoryTest extends \PHPUnit_Framework_TestCase {

/**
 * @expectedException \Gt\Database\Query\FactoryClassImplementationException
 */
public function testIncorrectQueryCollectionClass() {
	$factory = new QueryCollectionFactory(null, \DateTime::class);
}

}#