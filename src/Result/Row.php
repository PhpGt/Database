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
	return current($this->data);
}
public function key() {
	return key($this->data);
}
public function next() {
	next($this->data);
}
public function rewind() {
	reset($this->data);
}
public function valid() {
	return array_key_exists($this->key(), $this->data);
}

}#