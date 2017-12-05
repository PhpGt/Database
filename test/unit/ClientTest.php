<?php
namespace Gt\Database;

use Gt\Database\Connection\Settings;
use Gt\Database\Query\QueryCollection;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {
	public function testInterface() {
		$db = new Client();
		static::assertInstanceOf(Client::class, $db);
	}

	/**
	 * @dataProvider \Gt\Database\Test\Helper\Helper::queryCollectionPathExistsProvider
	 */
	public function testQueryCollectionPathExists(string $name, string $path) {
		$basePath = dirname($path);
		$settings = new Settings(
			$basePath,
			Settings::DRIVER_SQLITE,
			Settings::SCHEMA_IN_MEMORY
		);
		$db = new Client($settings);

		$queryCollection = $db->queryCollection($name);
		static::assertInstanceOf(QueryCollection::class, $queryCollection);
	}

	/**
	 * @dataProvider \Gt\Database\Test\Helper\Helper::queryPathNotExistsProvider
	 * @expectedException \Gt\Database\Query\QueryCollectionNotFoundException
	 */
	public function testQueryCollectionPathNotExists(string $name, string $path) {
		$basePath = dirname($path);

		$settings = new Settings(
			$basePath,
			Settings::DRIVER_SQLITE,
			Settings::SCHEMA_IN_MEMORY
		);
		$db = new Client($settings);
		$db->queryCollection($name);
	}
}