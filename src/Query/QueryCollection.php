<?php
namespace Gt\Database\Query;

use DirectoryIterator;
use Gt\Database\Connection\Driver;

class QueryCollection {

/** @var string */
private $directoryPath;
/** @var QueryFactory */
private $queryFactory;

public function __construct(
string $directoryPath, Driver $driver, QueryFactory $queryFactory = null) {
	if(is_null($queryFactory)) {
		$queryFactory = new QueryFactory($directoryPath, $driver);
	}

	$this->directoryPath = $directoryPath;
	$this->queryFactory = $queryFactory;
}

// TODO: PHP 7.1 iterable, to allow Gt\Database\Gt\Database\PlaceholderMap
public function query(
string $name, /*iterable*/array $placeholderMap = []):Query {
	$query = $this->queryFactory->create($name, $placeholderMap);
	return $query;
}

public function getDirectoryPath():string {
	return $this->directoryPath;
}

}#