<?php
namespace Gt\Database;

use Gt\Database\Connection\Settings;
use Gt\Database\Query\QueryCollection;
use Gt\Database\Query\QueryCollectionNotFoundException;

class ClientTest extends \PHPUnit_Framework_TestCase {

public function testInterface() {
	$db = new Client();
	$this->assertInstanceOf(Client::class, $db);
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryCollectionPathExistsProvider
 */
public function testQueryCollectionPathExists(string $name, string $path) {
	$basePath = dirname($path);
	$settings = new Settings(
		$basePath,
		Settings::DRIVER_SQLITE,
		Settings::DATABASE_IN_MEMORY
	);
	$db = new Client($settings);

	$this->assertTrue(isset($db[$name]));
	$queryCollection = $db->queryCollection($name);

	$this->assertInstanceOf(QueryCollection::class, $queryCollection);
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathNotExistsProvider
 * @expectedException QueryCollectionNotFoundException
 */
public function testQueryCollectionPathNotExists(string $name, string $path) {
	$basePath = dirname($path);

	$settings = new Settings(
		$basePath,
		Settings::DRIVER_SQLITE,
		Settings::DATABASE_IN_MEMORY
	);
	$db = new Client($settings);
	$this->assertFalse(isset($db[$name]));

	$db->queryCollection($name);
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryCollectionPathExistsProvider
 */
public function testOffsetGet(string $name, string $path) {
	$settings = new Settings(
		dirname($path),
		Settings::DRIVER_SQLITE,
		Settings::DATABASE_IN_MEMORY
	);
	$db = new Client($settings);

	$offsetGot = $db->offsetGet($name);
	$arrayAccessed = $db[$name];

	$this->assertEquals(
		$offsetGot->getDirectoryPath(),
		$arrayAccessed->getDirectoryPath()
	);
}

/**
 * @expectedException ReadOnlyArrayAccessException
 */
public function testOffsetSet() {
	$db = new Client();
	$db["test"] = "qwerty";
}

/**
 * @expectedException ReadOnlyArrayAccessException
 */
public function testOffsetUnset() {
	$db = new Client();
	unset($db["test"]);
}

}#