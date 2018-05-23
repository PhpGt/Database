<?php
namespace Gt\Database;

use Exception;
use Gt\Database\Connection\Driver;
use Gt\Database\Connection\Settings;
use Gt\Database\Test\Helper\Helper;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase {
	/** @var Driver */
	private $driver;
	/** @var Settings */
	private $settings;
	/** @var string */
	private $queryBase;
	/** @var Database */
	private $db;

	public function setUp() {
		$this->queryBase = Helper::getTmpDir() . "/query";

		$this->db = new Database($this->settingsSingleton());
		$driver = $this->db->getDriver();

		$connection = $driver->getConnection();
		$output = $connection->exec("CREATE TABLE test_table ( id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(32), number integer, timestamp DATETIME DEFAULT current_timestamp); CREATE UNIQUE INDEX test_table_name_uindex ON test_table (name);");

		if($output === false) {
			$error = $connection->errorInfo();
			throw new Exception($error[2]);
		}

		$insertStatement = $connection->prepare("INSERT INTO test_table (name, number) VALUES ('one', 1), ('two', 2), ('three', 3)");
		$success = $insertStatement->execute();
		if($success === false) {
			$error = $connection->errorInfo();
			throw new Exception($error[2]);
		}

		static::assertTrue($success, "Success inserting fake data");

		$selectStatement = $connection->query("SELECT * FROM test_table");
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
			"INSERT INTO test_table ( name ) VALUES (:nameToInsert)"
		);
		file_put_contents(
			$selectQueryPath,
			"SELECT * FROM test_table WHERE name = :rowName"
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
			"SELECT id, name, number FROM test_table WHERE id = ?"
		);

		$result2 = $this->db->fetch("exampleCollection/getById", 2);
		$result1 = $this->db->fetch("exampleCollection/getById", 1);

		$rqr = $this->db->executeSql("SELECT id, name FROM test_table");

		static::assertEquals(1, $result1->id);
		static::assertEquals(2, $result2->id);
		static::assertCount(3, $rqr);
	}

	public function testMultipleParameterUsage() {
		$queryCollectionPath = $this->queryBase . "/exampleCollection";
		$getByNameNumberQueryPath = $queryCollectionPath . "/getByNameNumber.sql";

		mkdir($queryCollectionPath, 0775, true);
		file_put_contents(
			$getByNameNumberQueryPath,
			"SELECT id, name, number FROM test_table WHERE name = :name and number = :number"
		);

		$result1 = $this->db->fetch("exampleCollection/getByNameNumber", [
			"name" => "one",
			"number" => 1,
		]);
		$result2 = $this->db->fetch("exampleCollection/getByNameNumber", [
			"name" => "two",
			"number" => 2,
		]);
		$resultNull = $this->db->fetch("exampleCollection/getByNameNumber", [
			"name" => "three",
			"number" => 55,
		]);

		$rqr = $this->db->executeSql("SELECT id, name FROM test_table");

		static::assertEquals(1, $result1->id);
		static::assertEquals(2, $result2->id);
		static::assertNull($resultNull);
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
}