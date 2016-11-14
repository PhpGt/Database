<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DriverInterface;

abstract class Query extends Builder implements QueryInterface {

/** @var string Absolute path to query file on disk */
private $filePath;
/** @var \Gt\Database\Connection\DriverInterface */
private $driver;

public function __construct(string $filePath, DriverInterface $driver) {
	if(!is_file($filePath)) {
		throw new QueryNotFoundException($filePath);
	}

	$this->filePath = $filePath;
	$this->driver = $driver;
	$this->execute();
}

public abstract function execute();

public function getFilePath():string {
	return $this->filePath;
}

}#