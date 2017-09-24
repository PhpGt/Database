<?php
namespace Gt\Database\Result;

use NonScrollableCursorException;
use PDO;
use Iterator;
use PDOStatement;

/**
 * @property int $length Number of rows represented, synonym of
 * count and getLength
 */
class ResultSet implements Iterator {

/** @var \PDOStatement */
protected $statement;
/** @var \Gt\Database\Result\Row */
protected $currentRow;
/** @var int */
protected $index = 0;
/** @var string */
protected $insertId = null;
/** @var Row[] */
protected $fetchAllCache = null;

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

	trigger_error(
		"Call to undefined method "
		. get_class($this)
		. "::"
		. $name
	,
		E_WARNING
	);
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

public function fetch():?Row {
	$data = $this->statement->fetch(
		PDO::FETCH_ASSOC
	);

	if(empty($data)) {
		return null;
	}

	$this->currentRow = new Row($data);
	return $this->currentRow;
}

// Iterator ////////////////////////////////////////////////////////////////////
public function rewind() {
	throw new NonScrollableCursorException();
}

public function current() {
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

}#
