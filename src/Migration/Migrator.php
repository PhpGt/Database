<?php
namespace Gt\Database\Migration;

use DirectoryIterator;
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
			"select `"
			. self::COLUMN_QUERY_NUMBER
			. "` from `{$this->tableName}` "
			. "order by 1 desc limit 1"
		);
		return (int)$result[self::COLUMN_QUERY_NUMBER];
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
				"`" . self::COLUMN_QUERY_NUMBER . "` int primary key,",
				"`" . self::COLUMN_QUERY_HASH . "` varchar(32) not null,",
				"`" . self::COLUMN_MIGRATED_AT . "` datetime not null )",
			]));
			echo "Created table `{$this->tableName}`." . PHP_EOL;
		}
		else {
			echo "Error getting migration count.";
			echo PHP_EOL;
			echo $message;
			echo PHP_EOL;
			exit(1);
		}
	}

	return 0;
}

public function getMigrationFileList():array {
	if(!is_dir($this->path)) {
		throw new MigrationException(
			"Migration directory not found: " . $this->path);
	}
	$fileList = scandir($this->path);
	natsort($fileList);

	$numberedFileList = [];

	foreach($fileList as $i => $file) {
		if($file[0] === ".") {
			continue;
		}

		$pathName = $this->path . "/" . $file;
		$fileNumber = (int)substr($file, 0, strpos($file, "-"));
		$numberedFileList[$fileNumber] = $pathName;
	}

	return $numberedFileList;
}

public function checkIntegrity(
	int $migrationCount = 0,
	array $migrationFileList
) {
	foreach($migrationFileList as $fileNumber => $file) {
		$md5 = md5_file($file);

		if($fileNumber <= $migrationCount) {
			$result = $this->dbClient->rawStatement(implode("\n", [
				"select `" . self::COLUMN_QUERY_HASH . "`",
				"from `{$this->tableName}`",
				"where `" . self::COLUMN_QUERY_NUMBER . "` = ?",
				"limit 1",
			]), [$fileNumber]);

			echo "Migration $fileNumber OK" . PHP_EOL;

			if($result[self::COLUMN_QUERY_HASH] !== $md5) {
				echo PHP_EOL;
				echo "Migration query doesn't match existing migration!";
				echo PHP_EOL;
				echo "Please check $file against your version control system.";
				echo PHP_EOL;
				exit(1);
			}

			continue;
		}
	}
}

public function performMigration(
	array $migrationFileList,
	int $existingMigrationCount = 0
) {
	foreach($migrationFileList as $fileNumber => $file) {
		if($fileNumber <= $existingMigrationCount) {
			continue;
		}

		try {
			echo "Migration $fileNumber: `$file`." . PHP_EOL;
			$sql = file_get_contents($file);
			$md5 = md5_file($file);
			$this->dbClient->rawStatement($sql);
		}
		catch(\Exception $exception) {
			echo "Error performing migration $fileNumber.";
			echo PHP_EOL;
			echo $exception->getMessage();
			echo PHP_EOL;
			exit(1);
		}

		$this->recordMigrationSuccess($fileNumber, $md5);
	}
}

private function recordMigrationSuccess(int $number, string $hash) {
	try {
		$this->dbClient->rawStatement(implode("\n", [
			"insert into `{$this->tableName}` (",
			"`" . self::COLUMN_QUERY_NUMBER . "`, ",
			"`" . self::COLUMN_QUERY_HASH . "`, ",
			"`" . self::COLUMN_MIGRATED_AT . "` ",
			") values (",
			"?, ?, now()",
			")",
		]), [$number, $hash]);
	}
	catch(\Exception $exception) {
		echo "Error storing migration progress in database table "
			. $this->tableName;
		echo PHP_EOL;
		echo $exception->getMessage();
		echo PHP_EOL;
		exit(1);
	}
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