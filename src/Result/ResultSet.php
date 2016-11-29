<?php
namespace Gt\Database\Result;

use Gt\Database\ReadOnlyArrayAccessException;
use PDO;
use ArrayAccess;
use Iterator;
use Countable;
use PDOStatement;
use JsonSerializable;

/**
 * @property int $length Number of rows represented, synonym of
 * count and getLength
 */
class ResultSet implements ArrayAccess, Iterator, Countable, JsonSerializable {

/** @var \PDOStatement */
private $statement;
/** @var \Gt\Database\Result\Row */
private $currentRow;
/** @var int */
private $index = 0;
/** @var string */
private $insertId = null;

public function __construct(PDOStatement $statement, string $insertId = null) {
	$statement->setFetchMode(PDO::FETCH_CLASS, Row::class);
	$this->statement = $statement;
	$this->insertId = $insertId;
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
	return count($this->fetchAll());
}

public function getAffectedRows():int {
	return $this->affectedRows();
}

public function affectedRows():int {
	return $this->statement->rowCount();
}

public function getLastInsertId():string {
	return $this->lastInsertId();
}

public function lastInsertId():string {
	return $this->insertId;
}

public function fetch(bool $skipIndexIncrement = false)/*:?Row*/ {
	$row = $this->statement->fetch(
		PDO::FETCH_CLASS,
		PDO::FETCH_ORI_NEXT,
		$this->index
	);

	if(is_null($row)) {
		$this->currentRow = null;
		return null;
	}
	else {
		$this->currentRow = $row;
	}

	if(!$skipIndexIncrement) {
		$this->index ++;
	}

	return $this->currentRow;
}

/**
 * @return Row[]
 */
public function fetchAll():array {
	$resultArray = [];

	foreach($this->statement->fetchAll() as $row) {
		$resultArray []= $row;
	}

	return $resultArray;
}

// ArrayAccess /////////////////////////////////////////////////////////////////

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

// Iterator ////////////////////////////////////////////////////////////////////
public function rewind() {
	$this->index = 0;
}

public function current() {
	$this->ensureFirstRowFetched();
	return $this->currentRow;
}

public function key() {
	return $this->index;
}

public function next() {
	$this->fetch();
}

public function valid():bool {
	return !empty($this->current());
}


private function ensureFirstRowFetched() {
	if(is_null($this->currentRow)) {
		$this->fetch(true);
	}
}

// JsonSerializable ////////////////////////////////////////////////////////////

public function jsonSerialize() {
	return $this->fetchAll();
}

}#