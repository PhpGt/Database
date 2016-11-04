<?php
namespace Gt\Database;

use Gt\Database\Query\QueryCollectionInterface;
use Gt\Database\Query\QueryCollectionFactory;
use Gt\Database\Connection\Settings;

class DatabaseClient implements DatabaseClientInterface {

/** @var QueryCollectionFactory */
private $queryCollectionFactory;
/** @var Settings */
private $connectionSettings;

public function __construct(Settings $connectionSettings = null,
QueryCollectionFactory $queryCollectionFactory = null) {
	if(is_null($connectionSettings)) {
		$connectionSettings = new Settings();
	}
	if(is_null($queryCollectionFactory)) {
		$queryCollectionFactory = new QueryCollectionFactory();
	}

	$this->connectionSettings = $connectionSettings;
	$this->queryCollectionFactory = $queryCollectionFactory;
}

/**
 * Synonym for ArrayAccess::offsetGet
 */
public function queryCollection(string $name):QueryCollectionInterface {
	return $this->queryCollectionFactory->create($name);
}

public function offsetExists($offset) {

}

public function offsetGet($offset) {
	return $this->queryCollection($offset);
}

public function offsetSet($offset, $value) {

}

public function offsetUnset($offset) {

}

}#