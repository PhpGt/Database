<?php
namespace Gt\Database\Cli;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;

class ExecuteCommand extends Command {
	public function run(ArgumentValueList $arguments = null):void {
		$forced = false;
		if(!empty($argv[1])
			&& ($argv[1] === "--force" || $argv[1] === "-f")) {
			$forced = true;
		}

		$defaultPath = implode(DIRECTORY_SEPARATOR, [
			$repoBasePath,
			"vendor",
			"phpgt",
			"webengine",
		]);
		foreach(["config.default.ini", "default.ini"] as $defaultFile) {
			$defaultFilePath = $defaultPath . DIRECTORY_SEPARATOR . $defaultFile;

			if(is_file($defaultFilePath)) {
				$defaultPath = $defaultFilePath;
			}
		}

		if(!is_file($defaultPath)) {
			$defaultPath = null;
		}

		$config = ConfigFactory::createForProject($repoBasePath);

		$default = $defaultPath
			? ConfigFactory::createFromPathName($defaultPath)
			: null;

		if($default) {
			$config->merge($default);
		}

		$settings = new Settings(
			implode(DIRECTORY_SEPARATOR, [
				$repoBasePath,
				$config->get("database.query_path")
			]),

			$config->get("database.driver") ?? 'mysql',
			$config->get("database.schema"),
			$config->get("database.host") ?? "localhost",
			$config->get("database.port") ?? "3306",
			$config->get("database.username"),
			$config->get("database.password")
		);

		$migrationPath = implode(DIRECTORY_SEPARATOR, [
			$repoBasePath,
			$config->get("database.query_path") ?? "query",
			$config->get("database.migration_path") ?? "_migration",
		]);
		$migrationTable = $config->get("database.migration_table") ?? "_migration";

		$migrator = new Migrator($settings, $migrationPath, $migrationTable, $forced);
		$migrator->createMigrationTable();
		$migrationCount = $migrator->getMigrationCount();
		$migrationFileList = $migrator->getMigrationFileList();
		$migrator->checkIntegrity($migrationFileList, $migrationCount);
		$migrator->performMigration($migrationFileList, $migrationCount);
	}

	public function getName():string {
		return "execute";
	}

	public function getDescription():string {
		return "Perform the migration";
	}

	public function getRequiredNamedParameterList():array {
		// TODO: Implement getRequiredNamedParameterList() method.
	}

	public function getOptionalNamedParameterList():array {
		// TODO: Implement getOptionalNamedParameterList() method.
	}

	public function getRequiredParameterList():array {
		// TODO: Implement getRequiredParameterList() method.
	}

	public function getOptionalParameterList():array {
		// TODO: Implement getOptionalParameterList() method.
	}
}
