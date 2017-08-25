<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Driver;
use Gt\Database\Result\ResultSet;

abstract class Query {

/** @var string Absolute path to query file on disk */
protected $filePath;
protected $connection;

public function __construct(string $filePath, Driver $driver) {
	if(!is_file($filePath)) {
		throw new QueryNotFoundException($filePath);
	}

	$this->filePath = $filePath;
	$this->connection = $driver->getConnection();
}

public function getFilePath():string {
	return $this->filePath;
}

abstract public function execute(array $bindings = []):ResultSet;

}#