<?php
namespace Gt\Database\Query;

use Gt\Database\Test\Helper;

class QueryCollectionFactoryTest extends \PHPUnit_Framework_TestCase {

/**
 * @expectedException \Gt\Database\Query\FactoryClassImplementationException
 */
public function testIncorrectQueryCollectionClass() {
	$factory = new QueryCollectionFactory(null, \DateTime::class);
}

public function testCurrentWorkingDirectoryDefault() {
	$queryCollectionName = "exampleTest";
	$baseDir = Helper::getTmpDir();
	$queryCollectionDirectoryPath = "$baseDir/$queryCollectionName";

	mkdir($queryCollectionDirectoryPath, 0775, true);
	chdir($baseDir);
	$queryCollectionFactory = new QueryCollectionFactory();
	$queryCollection = $queryCollectionFactory->create($queryCollectionName);

	$this->assertEquals(
		$queryCollectionDirectoryPath,
		$queryCollection->getDirectoryPath()
	);
}

}#