<?php
namespace Gt\Database\Migration;

use Gt\Database\Client;
use Gt\Database\Connection\Settings;

class Migrator {

const COLUMN_QUERY_NUMBER = "query_number";
const COLUMN_QUERY_HASH = "query_hash";
const COLUMN_MIGRATED_AT = "migrated_at";

private $schema;
private $dbClient;
private $path;
private $tableName;

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

public function getMigrationCount():int {
	try {
		$result = $this->dbClient->rawStatement(
			"select `{self::QUERY_NUMBER_COLUMN}` from `{$this->tableName}`
			order by 1 desc limit 1"
		);
		return (int)$result[self::QUERY_NUMBER_COLUMN];
	}
	catch(\Exception $exception) {
		$message = $exception->getMessage();
		$tableNotFoundError = preg_match(
			"/(SQLSTATE\[42S02\])|(Base table or view not found)/",
			$message
		);

		if($tableNotFoundError) {
			echo "Migration table not found, attempting to create." . PHP_EOL;
			$this->dbClient->rawStatement(implode("\n", [
				"create table `{$this->tableName}` (",
				"`{self::COLUMN_QUERY_NUMBER}` int primary key,"
				"`{self::COLUMN_QUERY_HASH}` varchar(32) not null,"
				"`{self::COLUMN_MIGRATED_AT}` datetime not null )"
			]));
			echo "Created table `{$this->tableName}`." . PHP_EOL;
		}
	}

	return 0;
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