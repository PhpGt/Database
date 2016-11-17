<?php
require "vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as CapsuleManager;

$capsule = new CapsuleManager();

$capsule->addConnection([
	"driver" => "sqlite",
	// "host" => "localhost",
	"database" => ":memory:",
	// "username" => "admin",
	// "password" => "",
	"charset" => "utf8",
	"collation" => "utf8_unicode_ci",
	"prefix" => "",
]);

/** @var \Illuminate\Database\Connection */
$connection = $capsule->getConnection();

$schemaBuilder = $connection->getSchemaBuilder();
$schemaBuilder->create("test_table", function($table) {
	$table->increments("id");
	$table->string("name")->unieque();
	$table->timestamps();
});

$insertStatement = $connection->getPdo()->prepare(
	"insert into test_table (id, name) values
	(100, 'binky'),
	(105, 'plinky'),
	(110, 'ungabi')"
	);

$success = $insertStatement->execute([]);
$first = $insertStatement->fetch();

var_dump($success, $first);

echo PHP_EOL. PHP_EOL. PHP_EOL. PHP_EOL;

$connection->getPdo()->setAttribute(
	PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statement = $connection->getPdo()->prepare(
	"select * from test_table limit 1");

$bindings = $connection->prepareBindings([
	// "id" => 105,
]);

$success = $statement->execute($bindings);
$foundRowsStatement = $connection->getPdo()->query(
	"SELECT FOUND_ROWS()");
var_dump($foundRowsStatement);die();
$first = $statement->fetch();

var_dump($success, $first);