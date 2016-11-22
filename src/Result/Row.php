<?php
namespace Gt\Database\Result;

use ArrayAccess;

class Row implements ArrayAccess {

/** @var array */
private $data;

public function __construct(array $data) {
	$this->data = $data;
}

public function offsetExists($offset) {
	return isset($this->data[$offset]);
}

public function offsetGet($offset) {
	return $this->data[$offset];
}

public function offsetSet($offset, $value) {
	$this->data[$offset] = $value;
}

public function offsetUnset($offset) {
	unset($this->data[$offset]);
}

}#