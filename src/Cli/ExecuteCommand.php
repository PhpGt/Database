<?php
namespace Gt\Database\Cli;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;
use Gt\Config\ConfigFactory;
use Gt\Database\Connection\Settings;
use Gt\Database\Migration\Migrator;

class ExecuteCommand extends Command {
	public function run(ArgumentValueList $arguments = null):void {
		$forced = $arguments->contains("force");

		$repoBasePath = $this->getRepoBasePath();
		$defaultPath = $this->getDefaultPath($repoBasePath);

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
			(int)($config->get("database.port") ?? "3306"),
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
		return [];
	}

	public function getOptionalNamedParameterList():array {
// TODO: It would be an improvement to allow passing database settings here rather than always require a config.ini
		return [];
	}

	public function getRequiredParameterList():array {
		return [];
	}

	public function getOptionalParameterList():array {
		return [
			new Parameter(
				false,
				"force",
				"f",
				"Forcefully drop the current schema and run from migration 1"
			)
		];
	}

	private function getDefaultPath(string $repoBasePath):?string {
		$defaultPath = implode(DIRECTORY_SEPARATOR, [
			$repoBasePath,
			"vendor",
			"phpgt",
			"webengine",
		]);
		foreach(["config.default.ini", "default.ini"] as $defaultFile) {
			$defaultFilePath = $defaultPath . DIRECTORY_SEPARATOR . $defaultFile;

			if(is_file($defaultFilePath)) {
				return $defaultFilePath;
			}
		}

		return null;
	}

	private function getRepoBasePath():string {
		$repoBasePath = __DIR__;
		$i = 0;

		do {
			$repoBasePath = dirname($repoBasePath);
			$i++;

			if($i > 10) {
// TODO: Decide if this functionality should be extracted into its own repo,
// maybe PhpGt/Path? If so, this should throw a PathException.
				return "";
			}
		}
		while(!is_dir($repoBasePath . DIRECTORY_SEPARATOR . "vendor"));
		return $repoBasePath;
	}
}
