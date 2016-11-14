<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DriverInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

abstract class Query implements QueryInterface {

/** @var string Absolute path to query file on disk */
private $filePath;

public function __construct(string $filePath, DriverInterface $driver) {
	if(!is_file($filePath)) {
		throw new QueryNotFoundException($filePath);
	}

	$this->filePath = $filePath;
	$this->capsule = new Capsule();
	$this->addCapsuleConnection($driver);
}

public function getFilePath():string {
	return $this->filePath;
}

private function addCapsuleConnection(DriverInterface $driver) {
	$this->capsule->addConnection([
	]);
}

}#