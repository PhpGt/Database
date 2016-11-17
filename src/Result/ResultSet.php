<?php
namespace Gt\Database\Result;

use Gt\Database\ReadOnlyArrayAccessException;
use PDOStatement;

/**
 * @property int $length Number of rows represented, synonym of
 * count and getLength
 */
class ResultSet implements ArrayAccess, Iterator, Countable {

/** @var \PDOStatement */
private $statement;
/** @var \Gt\Database\Result\Row */
private $currentRow;
/** @var array */
private $allRows;
/** @var int */
private $index = 0;

public function __construct(\PDOStatement $statement) {
	$this->statement = $statement;
}

public function __get($name) {
	$methodName = "get" . ucfirst($name);
	if(method_exists($this, $methodName)) {
		return $this->$methodName();
	}

	trigger_error(
		"Undefined property: "
		. self::class
		. "::$name"
	);
}

public function getLength():int {
	return $this->count();
}

public function count() {
	$this->allRows = $this->statement->fetchAll();
	return count($this->allRows);
}

public function fetch():Row {
	$this->currentRow = new Row(
		$this->statement->fetch()
	);

	return $this->currentRow;
}

public function affectedRows():int {
	return $this->statement->rowCount();
}

public function offsetExists($offset) {
	$this->ensureFirstRowFetched();
	return isset($this->currentRow[$offset]);
}

public function offsetGet($offset) {
	$this->ensureFirstRowFetched();
	return $this->currentRow[$offset];
}

public function offsetSet($offset, $value) {
	throw new ReadOnlyArrayAccessException($offset);
}

public function offsetUnset($offset) {
	throw new ReadOnlyArrayAccessException($offset);
}

private function ensureFirstRowFetched() {
	if(is_null($this->currentRow)) {
		$this->fetch();
	}
}

}#