<?php
namespace Gt\Database;

use DateTime;
use Gt\Database\Result\ResultSet;
use Gt\Database\Result\Row;

trait Fetchable {
	public function fetch(string $queryName, ...$bindings):?Row {
		/** @var ResultSet $result */
		$result = $this->query($queryName, ...$bindings);
		return $result->current();
	}

	public function fetchAll(string $queryName, ...$bindings):ResultSet {
		return $this->query($queryName, ...$bindings);
	}

	public function fetchBool(string $queryName, ...$bindings):?bool {
		return $this->fetchTyped(
			Type::BOOL,
			$queryName,
			$bindings
		);
	}

	public function fetchString(string $queryName, ...$bindings):?string {
		return $this->fetchTyped(
			Type::STRING,
			$queryName,
			$bindings
		);
	}

	public function fetchInt(string $queryName, ...$bindings):?int {
		return $this->fetchTyped(
			Type::INT,
			$queryName,
			$bindings
		);
	}

	public function fetchFloat(string $queryName, ...$bindings):?float {
		return $this->fetchTyped(
			Type::FLOAT,
			$queryName,
			$bindings
		);
	}

	public function fetchDateTime(string $queryName, ...$bindings):?DateTime {
		return $this->fetchTyped(
			Type::DATETIME,
			$queryName,
			$bindings
		);
	}

	/** @return bool[] */
	public function fetchAllBool(string $queryName, ...$bindings):array {
		return $this->fetchAllTyped(
			Type::BOOL,
			$queryName,
			$bindings
		);
	}

	/** @return string[] */
	public function fetchAllString(string $queryName, ...$bindings):array {
		return $this->fetchAllTyped(
			Type::STRING,
			$queryName,
			$bindings
		);
	}

	/** @return int[] */
	public function fetchAllInt(string $queryName, ...$bindings):array {
		return $this->fetchAllTyped(
			Type::INT,
			$queryName,
			$bindings
		);
	}

	/** @return float[] */
	public function fetchAllFloat(string $queryName, ...$bindings):array {
		return $this->fetchAllTyped(
			Type::FLOAT,
			$queryName,
			$bindings
		);
	}

	/** @return DateTime[] */
	public function fetchAllDateTime(string $queryName, ...$bindings):array {
		return $this->fetchAllTyped(
			Type::DATETIME,
			$queryName,
			$bindings
		);
	}

	protected function fetchTyped(
		string $type,
		string $queryName,
		...$bindings
	) {
		$row = $this->fetch($queryName, ...$bindings);
		if(is_null($row)) {
			return null;
		}

		return $this->castRow($type, $row);
	}

	protected function fetchAllTyped(
		string $type,
		string $queryName,
		...$bindings
	):array {
		$array = [];

		$resultSet = $this->fetchAll($queryName, ...$bindings);
		foreach($resultSet as $row) {
			$array []= $this->castRow($type, $row);
		}

		return $array;
	}

	protected function castRow(string $type, Row $row) {
		$assocArray = $row->asArray();
		reset($assocArray);
		$key = key($assocArray);
		$value = $assocArray[$key];

		switch($type) {
		case Type::BOOL:
		case "boolean":
			return (bool)$value;

		case Type::STRING:
			return (string)$value;

		case Type::INT:
		case "integer":
			return (int)$value;

		case Type::FLOAT:
			return (float)$value;

		case Type::DATETIME:
		case "datetime":
			return new DateTime($value);
		}
	}
}