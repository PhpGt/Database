<?php
namespace Gt\Database\Migration;

use Gt\Database\Client;
use Gt\Database\Connection\Settings;

class Migrator {

private $dbClient;
private $schema;
private $count = 0;
private $path;
private $table;

public function __construct(Settings $settings) {
	$this->schema = $settings->getDatabase();
	$this->dbClient = new Client($settings);
}

public function dropExistingSchema() {
	$this->dbClient->rawStatement("drop database if exists `{$schema}`");
}

}#