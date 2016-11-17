<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DriverInterface;

abstract class Query implements QueryInterface {

/** @var string Absolute path to query file on disk */
private $filePath;
/** @var \Illuminate\Database\Connection */
private $connection;

public function __construct(string $filePath, DriverInterface $driver) {
	if(!is_file($filePath)) {
		throw new QueryNotFoundException($filePath);
	}

	$this->filePath = $filePath;
	$this->connection = $driver->getConnection;
}

public function getFilePath():string {
	return $this->filePath;
}

}#