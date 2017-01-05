<?php
namespace Gt\Database\Result;

use ArrayAccess;
use Countable;
use Iterator;

class Row implements ArrayAccess, Countable, Iterator {

/** @var array */
private $data;
/** @var int */
private $iteratorIndex = 0;

public function __construct(array $data = []) {
	$this->data = $data;
}

// ArrayAccess /////////////////////////////////////////////////////////////////

public function offsetGet($offset) {
	return $this->data[$offset];
}

public function offsetSet($offset, $value) {
	$this->data[$offset] = $value;
}

public function offsetUnset($offset) {
	unset($this->data[$offset]);
}

public function offsetExists($offset) {
	return isset($this->data[$offset]);
}

// Countable ///////////////////////////////////////////////////////////////////

public function count() {
	return count($this->data);
}

// Iterator ////////////////////////////////////////////////////////////////////

public function current() {
	return $this->data[$this->iteratorIndex];
}
public function key() {
	return $this->iteratorIndex;
}
public function next() {
	++ $this->iteratorIndex;
}
public function rewind() {
	$this->iteratorIndex = 0;
}
public function valid() {
	return isset($this->data[$this->iteratorIndex]);
}

}#