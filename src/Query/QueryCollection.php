<?php
namespace Gt\Database\Query;

use DirectoryIterator;

class QueryCollection implements QueryCollectionInterface {

/** @var string */
private $directoryPath;
/** @var QueryFactoryInterface */
private $queryFactory;

public function __construct(
string $directoryPath, QueryFactoryInterface $queryFactory = null) {
	if(is_null($queryFactory)) {
		$queryFactory = new QueryFactory($directoryPath);
	}

	$this->directoryPath = $directoryPath;
	$this->queryFactory = $queryFactory;
}

// TODO: PHP 7.1 iterable, to allow Gt\Database\Gt\Database\PlaceholderMap
public function query(
string $name, /*iterable*/array $placeholderValueMap = []):QueryInterface {
	$query = $this->queryFactory->create($name, $placeholderValueMap);
	return $query;
}

public function getDirectoryPath():string {
	return $this->directoryPath;
}

}#