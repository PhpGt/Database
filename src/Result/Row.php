<?php
namespace Gt\Database\Result;

use Countable;
use Iterator;

class Row implements Countable, Iterator {

/** @var array */
private $data;

public function __construct(array $data = []) {
	$this->data = $data;
}

public function __get($name) {
	if (!isset($this->$name)) {
		throw new NoSuchColumnException($name);
	}

	return $this->data[$name];
}

public function __isset($name) {
	return array_key_exists($name, $this->data);
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
