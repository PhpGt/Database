<?php
namespace Gt\Database;

use Exception;
use Gt\Database\Connection\Driver;
use Gt\Database\Connection\Settings;
use Gt\Database\Test\Helper;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase {

/** @var Driver */
private $driver;
/** @var Settings */
private $settings;
/** @var string */
private $queryBase;
/** @var Client */
private $db;

public function setUp() {
	$this->queryBase = Helper::getTmpDir() . "/query";

	$this->db = new Client($this->settingsSingleton());
	$driver = $this->db->getDriver();

	$connection = $driver->getConnection();
	$output = $connection->exec("CREATE TABLE test_table ( id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(32), timestamp DATETIME DEFAULT current_timestamp); CREATE UNIQUE INDEX test_table_name_uindex ON test_table (name);");

	if($output === false) {
		$error = $connection->errorInfo();
		throw new Exception($error[2]);
	}

	$insertStatement = $connection->prepare("insert into test_table (name) values ('one'), ('two'), ('three')");
	$success = $insertStatement->execute();
	if($success === false) {
		$error = $connection->errorInfo();
		throw new Exception($error[2]);
	}

	static::assertTrue($success, "Success inserting fake data");

	$selectStatement = $connection->query("select * from test_table");
	$result = $selectStatement->fetchAll();
	static::assertCount(3, $result);
}

public function testSubsequentSqlQueries() {
	$uuid = uniqid();
	$queryCollectionPath = $this->queryBase . "/exampleCollection";
	$insertQueryPath = $queryCollectionPath . "/insert.sql";
	$selectQueryPath = $queryCollectionPath . "/selectByName.sql";

	mkdir($queryCollectionPath, 0775, true);
// placeholders are specifically not named the same.
	file_put_contents(
		$insertQueryPath,
		"insert into test_table ( name ) values (:nameToInsert)"
	);
	file_put_contents(
		$selectQueryPath,
		"select * from test_table where name = :rowName"
	);

	$this->db->insert("exampleCollection/insert", [
		"nameToInsert" => $uuid,
	]);
	$result = $this->db->fetch("exampleCollection/selectByName", [
		"rowName" => $uuid,
	]);

	static::assertEquals($uuid, $result->name);

// perform an insert and select again:
	$uuid2 = uniqid();
	$this->db->insert("exampleCollection/insert", [
		"nameToInsert" => $uuid2,
	]);
	$result1 = $this->db->fetch("exampleCollection/selectByName", [
		"rowName" => $uuid,
	]);
	$result2 = $this->db->fetch("exampleCollection/selectByName", [
		"rowName" => $uuid2,
	]);

	static::assertEquals($uuid, $result1->name);
	static::assertEquals($uuid2, $result2->name);
}

public function testQuestionMarkParameter() {
	$queryCollectionPath = $this->queryBase . "/exampleCollection";
	$getByIdQueryPath = $queryCollectionPath . "/getById.sql";

	mkdir($queryCollectionPath, 0775, true);
	file_put_contents(
		$getByIdQueryPath,
		"select id, name from test_table where id = ?"
	);

	$result2 = $this->db->fetch("exampleCollection/getById", 2);
	$result1 = $this->db->fetch("exampleCollection/getById", 1);

	$rqr = $this->db->executeSql("select id, name from test_table");

	static::assertEquals(1, $result1->id);
	static::assertEquals(2, $result2->id);
	static::assertCount(3, $rqr);
}

private function settingsSingleton():Settings {
	if(is_null($this->settings)) {
		$this->settings = new Settings(
			$this->queryBase,
			Settings::DRIVER_SQLITE,
			Settings::SCHEMA_IN_MEMORY,
			"localhost"
		);
	}

	return $this->settings;
}

}#
