<?php
namespace Gt\Database\Result;

use ArrayAccess;

class Row implements ArrayAccess {

public function offsetGet($offset) {
	return $this->$offset;
}

public function offsetSet($offset, $value) {
	$this->$offset = $value;
}

public function offsetUnset($offset) {
	unset($this->$offset);
}

public function offsetExists($offset) {
	return isset($this->$offset);
}

}#