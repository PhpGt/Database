<?php
namespace Gt\Database\Result;

use Countable;
use Iterator;

class Row implements Iterator {

/** @var array */
protected $data;

public function __construct(array $data = []) {
	$this->data = $data;
	$this->setProperties($data);
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

protected function setProperties(array $data) {
	foreach($data as $key => $value) {
		$this->$key = $value;
	}
}

// Iterator ////////////////////////////////////////////////////////////////////
protected $iterator_index = 0;
protected $iterator_data_key_list = [];

public function rewind():void {
	$this->iterator_index = 0;
	$this->iterator_data_key_list = array_keys($this->data);
}
public function current() {
	$key = $this->key();
	return $this->$key;
}

public function key():?string {
	return $this->iterator_data_key_list[$this->iterator_index];
}

public function next():void {
	$this->iterator_index ++;
}

public function valid():bool {
	return isset($this->iterator_data_key_list[$this->iterator_index]);
}

}#
