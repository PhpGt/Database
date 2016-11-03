<?php
namespace Gt\Database;

use Gt\Database\Entity\TableCollectionInterface;
use Gt\Database\Entity\TableCollectionFactory;
use Gt\Database\Connection\Settings;

class Client implements ClientInterface {

/** @var TableCollectionFactory */
private $tableCollectionFactory;
/** @var Settings */
private $connectionSettings;

public function __construct(Settings $connectionSettings = null,
TableCollectionFactory $tableCollectionFactory = null) {
	if(is_null($connectionSettings)) {
		$connectionSettings = new Settings();
	}
	if(is_null($tableCollectionFactory)) {
		$tableCollectionFactory = new TableCollectionFactory();
	}

	$this->connectionSettings = $connectionSettings;
	$this->tableCollectionFactory = $tableCollectionFactory;
}

/**
 * Synonym for ArrayAccess::offsetGet
 */
public function tableCollection(string $name):TableCollectionInterface {
	return $this->tableCollectionFactory->create($name);
}

public function offsetExists($offset) {

}

public function offsetGet($offset) {
	return $this->tableCollection($offset);
}

public function offsetSet($offset, $value) {

}

public function offsetUnset($offset) {

}

}#