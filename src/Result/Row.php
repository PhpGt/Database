<?php
namespace Gt\Database\Result;

use DateTimeImmutable;
use DateTimeInterface;
use Iterator;

class Row implements Iterator {
	/** @var array<string, string> */
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
		return $this->data[$columnName] ?? null;
	}

	public function getString(string $columnName):?string {
		return $this->getAsNullablePrimitive($columnName, "string");
	}

	public function getInt(string $columnName):?int {
		return $this->getAsNullablePrimitive($columnName, "int");
	}

	public function getFloat(string $columnName):?float {
		return $this->getAsNullablePrimitive($columnName, "float");
	}

	public function getBool(string $columnName):?bool {
		return $this->getAsNullablePrimitive($columnName, "bool");
	}

	public function getDateTime(string $columnName):?DateTimeInterface {
		$dateString = $this->data[$columnName] ?? null;
		if(is_null($dateString)) {
			return null;
		}

		if(is_numeric($dateString)) {
			$dateTime = new DateTimeImmutable();
			return $dateTime->setTimestamp($dateString);
		}

		return new DateTimeImmutable($dateString);
	}

	public function asArray():array {
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

	/** @return mixed */
	private function getAsNullablePrimitive(string $key, string $type) {
		$value = $this->get($key);
		if(is_null($value)) {
			return null;
		}

		switch($type) {
		case "string":
			return $value;

		case "int":
		case "integer":
			return (int)$value;

		case "float":
		case "double":
		case "decimal":
			return (float)$value;

		case "bool":
		case "boolean":
			return (bool)$value;
		}

		throw new InvalidNullablePrimitiveException($type);
	}
}
