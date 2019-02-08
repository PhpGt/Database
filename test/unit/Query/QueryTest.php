<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Driver;
use Gt\Database\Connection\DefaultSettings;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase {
	/** @dataProvider \Gt\Database\Test\Helper\Helper::queryPathNotExistsProvider */
	public function testConstructionQueryPathNotExists(
		string $queryName,
		string $queryCollectionPath,
		string $queryPath
	) {
		self::expectException(QueryNotFoundException::class);
		new SqlQuery($queryPath, new Driver(new DefaultSettings()));
	}

	/**
	 * @dataProvider \Gt\Database\Test\Helper\Helper::queryPathExistsProvider
	 */
	public function testConstructionQueryPathExists(
		string $queryName,
		string $queryCollectionPath,
		string $queryPath
	) {
		try {
			$query = new SqlQuery($queryPath, new Driver(new DefaultSettings()));
			static::assertFileExists($query->getFilePath());
		}
		catch(\Exception $e) {
		}
	}
}