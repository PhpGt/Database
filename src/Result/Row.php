<?php
namespace Gt\Database\Result;

use DateTime;
use Iterator;

class Row implements Iterator {
	/** @var array */
	protected $data;
	protected $iterator_index = 0;
	protected $iterator_data_key_list = [];

	public function __construct(array $data = []) {
		$this->data = $data;
	}

	public function __get(string $name):?string {
		return $this->getString($name);
	}

	public function __isset(string $name):bool {
		return array_key_exists($name, $this->data);
	}

	public function get(string $columnName):?string {
		return $this->getString($columnName);
	}

	public function getString(string $columnName):?string {
		return $this->data[$columnName] ?? null;
	}

	public function getInt(string $columnName):?int {
		return (int)$this->data[$columnName] ?? null;
	}

	public function getFloat(string $columnName):?float {
		return (float)$this->data[$columnName] ?? null;
	}

	public function getBool(string $columnName):?bool {
		return (bool)$this->data[$columnName] ?? null;
	}

	public function getDateTime(string $columnName):?DateTime {
		$dateString = $this->data[$columnName];
		if(is_null($dateString)) {
			return null;
		}

		if(is_numeric($dateString)) {
			$dateTime = new DateTime();
			$dateTime->setTimestamp($dateString);
			return $dateTime;
		}

		$dateTime = new DateTime($dateString);
		if(!$dateTime) {
			throw new BadlyFormedDataException($columnName);
		}

		return $dateTime;
	}

	public function toArray():array {
		return $this->data;
	}

	public function contains(string $name):bool {
		return $this->__isset($name);
	}

	public function rewind():void {
		$this->iterator_index = 0;
		$this->iterator_data_key_list = array_keys($this->data);
	}

	public function key():?string {
		return $this->iterator_data_key_list[
			$this->iterator_index
			] ?? null;
	}

	public function next():void {
		$this->iterator_index++;
	}

	public function valid():bool {
		return isset($this->iterator_data_key_list[
			$this->iterator_index
			]);
	}

	public function current():?string {
		$key = $this->key();
		return $this->$key;
	}
}