<?php
namespace Gt\Database\Test;

use Gt\Database\Connection\Driver;
use Gt\Database\Connection\Settings;
use Gt\Database\Query\SqlQuery;
use PHPUnit\Framework\TestCase;
use Gt\Database\Test\Helper\Helper;

class SqlQueryTest extends TestCase {
	/** @var Driver */
	private $driver;

	public function setUp() {
		$driver = $this->driverSingleton();
		$connection = $driver->getConnection();
		$output = $connection->exec("CREATE TABLE test_table ( id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(32), timestamp DATETIME DEFAULT current_timestamp); CREATE UNIQUE INDEX test_table_name_uindex ON test_table (name);");
		static::assertNotFalse($output);

		$insertStatement = $connection->prepare(
			"INSERT INTO test_table (name) VALUES
		('one'),
		('two'),
		('three')"
		);
		$success = $insertStatement->execute();
		static::assertTrue($success, "Success inserting fake data");
	}

	/**
	 * @dataProvider \Gt\Database\Test\Helper\Helper::queryPathNotExistsProvider
	 * @expectedException \Gt\Database\Query\QueryNotFoundException
	 */
	public function testQueryNotFound(
		string $queryName,
		string $queryCollectionPath,
		string $queryPath
	) {
		new SqlQuery($queryPath, $this->driverSingleton());
	}

	/**
	 * @dataProvider \Gt\Database\Test\Helper\Helper::queryPathExistsProvider
	 */
	public function testQueryFound(
		string $queryName,
		string $queryCollectionPath,
		string $queryPath
	) {
		$query = new SqlQuery($queryPath, $this->driverSingleton());
		static::assertFileExists($query->getFilePath());
	}

	/**
	 * @dataProvider \Gt\Database\Test\Helper\Helper::queryPathExistsProvider
	 * @expectedException \Gt\Database\Query\PreparedStatementException
	 */
	public function testBadPreparedStatementThrowsException(
		string $queryName,
		string $queryCollectionPath,
		string $queryPath
	) {
		file_put_contents($queryPath, "insert blahblah into nothing");
		$query = new SqlQuery($queryPath, $this->driverSingleton());
		$query->execute();
	}

	/**
	 * @dataProvider \Gt\Database\Test\Helper\Helper::queryPathExistsProvider
	 */
	public function testPreparedStatement(
		string $queryName,
		string $queryCollectionPath,
		string $queryPath
	) {
		file_put_contents($queryPath, "SELECT * FROM test_table");
		$query = new SqlQuery($queryPath, $this->driverSingleton());
		$resultSet = $query->execute();

		foreach(["one", "two", "three"] as $i => $name) {
			$row = $resultSet->fetch();
			static::assertEquals($i + 1, $row->id);
			static::assertEquals($name, $row->name);
		}
	}

	/**
	 * @dataProvider \Gt\Database\Test\Helper\Helper::queryPathExistsProvider
	 */
	public function testLastInsertId(
		string $queryName,
		string $queryCollectionPath,
		string $queryPath
	) {
		$uuid = uniqid("test-");
		file_put_contents($queryPath, "insert into test_table (name) values ('$uuid')");
		$query = new SqlQuery($queryPath, $this->driverSingleton());
		$resultSet = $query->execute();
		$id = $resultSet->lastInsertId();
		static::assertNotEmpty($id);

		file_put_contents($queryPath, "select * from test_table where id = $id");
		$query = new SqlQuery($queryPath, $this->driverSingleton());
		$resultSet = $query->execute();

		$row = $resultSet->fetch();
		static::assertEquals($uuid, $row->name);
	}

	public function testSubsequentCounts() {
		$testData = Helper::queryPathExistsProvider();
		$queryPath = $testData[0][2];
		file_put_contents($queryPath, "SELECT * FROM test_table");
		$query = new SqlQuery($queryPath, $this->driverSingleton());
		$resultSet = $query->execute();
		$count = count($resultSet);
		static::assertGreaterThan(0, $count);
		static::assertCount($count, $resultSet);
	}

	public function testSubsequentCalls() {
		$testData = [
			Helper::queryPathExistsProvider(),
			Helper::queryPathExistsProvider(),
		];
		$queryPath = [
			$testData[0][0][2],
			$testData[1][0][2],
		];

		$lastTestWord = "";

		foreach(["Hello", "Goodbye"] as $i => $testWord) {
			static::assertNotEquals($testWord, $lastTestWord);
			file_put_contents($queryPath[$i], "select '$testWord' as test");
			$query = new SqlQuery($queryPath[$i], $this->driverSingleton());
			$resultSet = $query->execute();
			$row = $resultSet->fetch();
			static::assertEquals($testWord, $row->test);
			$lastTestWord = $testWord;
		}
	}

	/**
	 * @dataProvider \Gt\Database\Test\Helper\Helper::queryPathExistsProvider
	 */
	public function testPlaceholderReplacement(
		string $queryName,
		string $queryCollectionPath,
		string $queryPath
	) {
		$uuid = uniqid("test-");
		file_put_contents($queryPath, "select :testPlaceholder as `testValue`");
		$query = new SqlQuery($queryPath, $this->driverSingleton());
		$resultSet = $query->execute([
			"testPlaceholder" => $uuid,
		]);

		$row = $resultSet->fetch();
		static::assertEquals($uuid, $row->testValue);
	}

	/**
	 * @dataProvider \Gt\Database\Test\Helper\Helper::queryPathExistsProvider
	 */
	public function testPlaceholderReplacementInComments(
		string $queryName,
		string $queryCollectionPath,
		string $queryPath
	) {
		$uuid = uniqid("test-");
// The question mark could cause problems with preparing queries.
		file_put_contents($queryPath, "select :test as `test` -- does this test work?");
		$query = new SqlQuery($queryPath, $this->driverSingleton());
		$resultSet = $query->execute([
			"test" => $uuid,
		]);

		$row = $resultSet->fetch();
		static::assertEquals($uuid, $row->test);
	}

	public function testPlaceholderReplacementSubsequentCalls() {
		$pathDataList = Helper::queryPathExistsProvider();

		$testQueryList = [
			"select :testPlaceholder as `testPlaceholder`",
			"select :firstPlaceholder as `firstPlaceholder`, :secondPlaceholder as `secondPlaceholder`",
		];
		$placeholderList = [
			[
				"testPlaceholder" => uniqid()
			],
			[
				"firstPlaceholder" => uniqid("first"),
				"secondPlaceholder" => uniqid("second"),
			]
		];

		foreach([$pathDataList[0], $pathDataList[1]] as $i => $pathData) {
			$queryName = $pathData[0];
			$queryCollectionPath = $pathData[1];
			$queryPath = $pathData[2];

			file_put_contents($queryPath, $testQueryList[$i]);
			$query = new SqlQuery($queryPath, $this->driverSingleton());
			$resultSet = $query->execute($placeholderList[$i]);
			$row = $resultSet->fetch();

			static::assertCount(
				count($placeholderList[$i]),
				$row,
				"Iteration $i"
			);

			foreach($placeholderList[$i] as $key => $value) {
				static::assertEquals($value, $row->$key, "Iteration $i");
			}
		}
	}

	private function driverSingleton():Driver {
		if(is_null($this->driver)) {
			$settings = new Settings(
				Helper::getTmpDir(),
				Settings::DRIVER_SQLITE,
				Settings::SCHEMA_IN_MEMORY
			);
			$this->driver = new Driver($settings);
		}

		return $this->driver;
	}
}
