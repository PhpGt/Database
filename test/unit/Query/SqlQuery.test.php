<?php
namespace Gt\Database\Test;

use Gt\Database\Connection\Driver;
use Gt\Database\Connection\Settings;
use Gt\Database\Query\PreparedStatementException;
use Gt\Database\Query\QueryNotFoundException;
use Gt\Database\Query\SqlQuery;

class SqlQueryTest extends \PHPUnit_Framework_TestCase {

/** @var Driver */
private $driver;

public function setUp() {
	$driver = $this->driverSingleton();
	$connection = $driver->getConnection();
	$schemaBuilder = $connection->getSchemaBuilder();
	$schemaBuilder->create("test_table", function($table) {
		$table->increments("id");
		$table->string("name")->unique();
		$table->timestamps();
	});
	$insertStatement = $connection->getPdo()->prepare(
		"insert into test_table (name) values
		('one'),
		('two'),
		('three')"
	);
	$success = $insertStatement->execute();
	$this->assertTrue($success, "Success inserting fake data");
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathNotExistsProvider
 * @expectedException QueryNotFoundException
 */
public function testQueryNotFound(
	string $queryName,
	string $queryCollectionPath,
	string $queryPath
) {
	new SqlQuery($queryPath, $this->driverSingleton());
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 */
public function testQueryFound(
	string $queryName,
	string $queryCollectionPath,
	string $queryPath
) {
	$query = new SqlQuery($queryPath, $this->driverSingleton());
	$this->assertFileExists($query->getFilePath());
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 * @expectedException PreparedStatementException
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
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
 */
public function testPreparedStatement(
	string $queryName,
	string $queryCollectionPath,
	string $queryPath
) {
	file_put_contents($queryPath, "select * from test_table");
	$query = new SqlQuery($queryPath, $this->driverSingleton());
	$resultSet = $query->execute();

	foreach(["one", "two", "three"] as $i => $name) {
		$row = $resultSet->fetch();
		$this->assertEquals($i + 1, $row->id);
		$this->assertEquals($name, $row->name);
	}
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
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
	$id = $resultSet->lastInsertId;
	$this->assertNotEmpty($id);

	file_put_contents($queryPath, "select * from test_table where id = $id");
	$query = new SqlQuery($queryPath, $this->driverSingleton());
	$resultSet = $query->execute();

	$this->assertEquals($uuid, $resultSet->name);
}

public function testSubsequentCounts() {
	$testData = Helper::queryPathExistsProvider();
	$queryPath = $testData[0][2];
	file_put_contents($queryPath, "select * from test_table");
	$query = new SqlQuery($queryPath, $this->driverSingleton());
	$resultSet = $query->execute();
	$count = count($resultSet);
	$this->assertGreaterThan(0, $count);
	$this->assertCount($count, $resultSet);
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

	foreach(["Hello","Goodbye"] as $i => $testWord) {
		$this->assertNotEquals($testWord, $lastTestWord);
		file_put_contents($queryPath[$i], "select '$testWord' as test");
		$query = new SqlQuery($queryPath[$i], $this->driverSingleton());
		$resultSet = $query->execute();
		$this->assertEquals($testWord, $resultSet->test);
		$lastTestWord = $testWord;
	}
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
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

	$this->assertEquals($uuid, $resultSet->testValue);
}

/**
 * @dataProvider \Gt\Database\Test\Helper::queryPathExistsProvider
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

	$this->assertEquals($uuid, $resultSet->test);
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
		], [
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

		$this->assertCount(
			count($placeholderList[$i]),
			$row,
			"Iteration $i"
		);

		foreach($placeholderList[$i] as $key => $value) {
			$this->assertEquals($value, $row->$key, "Iteration $i");
		}
	}
}

private function driverSingleton():Driver {
	if(is_null($this->driver)) {
		$settings = new Settings(
			Helper::getTmpDir(),
			Settings::DRIVER_SQLITE,
			Settings::DATABASE_IN_MEMORY
		);
		$this->driver = new Driver($settings);
	}

	return $this->driver;
}

}#
