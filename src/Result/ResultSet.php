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
/** @var Row[] */
private $fetchAllCache = null;

public function __construct(PDOStatement $statement, string $insertId = null) {
	$this->statement = $statement;
	$this->insertId = $insertId;
}

/** @throws EmptyResultSetException If you try to access a column when no
 * results were returned*/
public function __get($name) {
	$methodName = "get" . ucfirst($name);
	if(method_exists($this, $methodName)) {
		return $this->$methodName();
	}

	$this->ensureFirstRowFetched();
	if($this->currentRow === null) {
	    throw new EmptyResultSetException();
    }

	return $this->currentRow->$name;
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

public function hasResult():bool {
	$this->ensureFirstRowFetched();
	return !is_null($this->currentRow);
}

public function fetch(bool $skipIndexIncrement = false)/*:?Row*/ {
	$data = $this->statement->fetch(
		PDO::FETCH_ASSOC,
		PDO::FETCH_ORI_NEXT,
		$this->index
	);

	if(empty($data)) {
		$this->currentRow = null;
		return null;
	}
	else {
		$row = new Row($data);
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
	if(!is_null($this->fetchAllCache)) {
		return $this->fetchAllCache;
	}

	$this->fetchAllCache = [];

	foreach($this->statement->fetchAll() as $row) {
		$this->fetchAllCache []= new Row($row);
	}

	return $this->fetchAllCache;
}

// ArrayAccess /////////////////////////////////////////////////////////////////

public function offsetExists($offset) {
	$allRows = $this->fetchAll();
	return isset($allRows[$offset]);
}

public function offsetGet($offset) {
	$allRows = $this->fetchAll();
	return $allRows[$offset];
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
