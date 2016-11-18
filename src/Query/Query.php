<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Driver;

abstract class Query {

/** @var string Absolute path to query file on disk */
protected $filePath;
/** @var \Illuminate\Database\Connection */
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

}#