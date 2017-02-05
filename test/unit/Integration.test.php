<?php
namespace Gt\Database;

use Gt\Database\Client;
use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Driver;
use Gt\Database\Connection\Settings;
use Gt\Database\Query\QueryCollection;
use Gt\Database\Query\QueryCollectionFactory;
use Gt\Database\Test\Helper;

class IntegrationTest extends \PHPUnit_Framework_TestCase {

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
	$schemaBuilder = $connection->getSchemaBuilder();
	$schemaBuilder->create("test_table", function($table) {
		$table->increments("id");
		$table->string("name")->unique();
		$table->timestamps();
	});

	$this->assertTrue(
		$schemaBuilder->hasTable("test_table"),
		"test_table table exists."
	);

	$insertStatement = $connection->getPdo()->prepare(
		"insert into test_table (name) values
		('one'),
		('two'),
		('three')"
	);
	$success = $insertStatement->execute();

	$result = $connection->table("test_table")->get();
	$this->assertCount(3, $result);

	$this->assertTrue($success, "Success inserting fake data");
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

	$this->db["exampleCollection"]->insert([
		"nameToInsert" => $uuid,
	]);
	$result = $this->db["exampleCollection"]->selectByName([
		"rowName" => $uuid,
	]);

	$this->assertEquals($uuid, $result["name"]);

// perform an insert and select again:
	$uuid2 = uniqid();
	$this->db["exampleCollection"]->insert([
		"nameToInsert" => $uuid2,
	]);
	$result1 = $this->db["exampleCollection"]->selectByName([
		"rowName" => $uuid,
	]);
	$result2 = $this->db["exampleCollection"]->selectByName([
		"rowName" => $uuid2,
	]);

	$this->assertEquals($uuid, $result1["name"]);
	$this->assertEquals($uuid2, $result2["name"]);
}

public function testQuestionMarkParameter() {
	$uuid = uniqid();
	$queryCollectionPath = $this->queryBase . "/exampleCollection";
	$getByIdQueryPath = $queryCollectionPath . "/getById.sql";

	mkdir($queryCollectionPath, 0775, true);
	file_put_contents(
		$getByIdQueryPath,
		"select id, name from test_table where id = ?"
	);

	$result2 = $this->db["exampleCollection"]->getById(2);
	$result1 = $this->db["exampleCollection"]->getById(1);

	$rqr = $this->db->rawQuery("select id, name from test_table");

	$this->assertEquals(1, $result1["id"]);
	$this->assertEquals(2, $result2["id"]);
}

private function settingsSingleton():Settings {
	if(is_null($this->settings)) {
		$this->settings = new Settings(
			$this->queryBase,
			Settings::DRIVER_SQLITE,
			Settings::DATABASE_IN_MEMORY,
			"localhost"
		);
	}

	return $this->settings;
}

private function driverSingleton():Driver {
	if(is_null($this->driver)) {
		$this->driver = new Driver($this->settingsSingleton());
	}

	return $this->driver;
}

}#