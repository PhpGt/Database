<?php
namespace Gt\Database\Migration;

use Gt\Database\Client;
use Gt\Database\Connection\Settings;

class Migrator {

private $schema;
private $dbClient;
private $path;
private $tableName;
private $count;

public function __construct(
	Settings $settings,
	string $path,
	string $tableName,
	bool $forced
) {
	$this->schema = $settings->getDatabase();
	$this->path = $path;
	$this->tableName = $tableName;

	$settingsWithoutSchema = new Settings(
		$settings->getBaseDirectory(),
		$settings->getDataSource(),
		// Schema may not exist yet.
		"",
		$settings->getHost(),
		$settings->getPort(),
		$settings->getUsername(),
		$settings->getPassword()
	);

	$this->dbClient = new Client($settingsWithoutSchema);
	$this->selectSchema($forced);
}

private function selectSchema(bool $deleteAndRecreateSchema = false) {
	if($deleteAndRecreateSchema) {
		$this->deleteAndRecreateSchema();
	}

	$schema = $this->schema;

	try {
		$this->dbClient->rawStatement("use `$schema`");
	}
	catch(\Exception $exception) {
		echo "Error selecting `$schema`." . PHP_EOL;
		echo $exception->getMessage() . PHP_EOL;
		exit(1);
	}
}

private function deleteAndRecreateSchema() {
	$schema = $this->schema;

	try {
		$this->dbClient->rawStatement("drop schema if exists `$schema`");
		$this->dbClient->rawStatement("create schema if not exists `$schema`");
	}
	catch(\Exception $exception) {
		echo "Error recreating schema `$schema`." . PHP_EOL;
		echo $exception->getMessage() . PHP_EOL;
		exit(1);
	}
}

}#