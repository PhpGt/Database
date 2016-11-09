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

public function query(
string $name, array $placeholderValueMap = []):QueryInterface {
	return $this->queryFactory->create($name, $placeholderValueMap);
}

public function getDirectoryPath():string {
	return $this->directoryPath;
}

}#