<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\DriverInterface;
use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Illuminate\Database\Connection;

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
	$this->connection = $this->createConnection(new CapsuleManager(), $driver);
}

public function getFilePath():string {
	return $this->filePath;
}

public function createConnection(
CapsuleManager $capsuleManager = null,
DriverInterface $driver = null
):Connection {
	// $capsuleManager->addConnection([
		// 'driver'    => 'mysql',
		// 'host'      => 'localhost',
		// 'database'  => 'phpgt_test',
		// 'username'  => 'admin',
		// 'password'  => '',
		// 'charset'   => 'utf8',
		// 'collation' => 'utf8_unicode_ci',
		// 'prefix'    => '',
	// ]);

	return $capsuleManager->getConnection();
}

}#