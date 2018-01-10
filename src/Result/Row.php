<?php
namespace Gt\Database\Result;

use Countable;

class Row implements Countable {
	public function __construct(array $data = []) {
		$this->setProperties($data);
	}

	protected function setProperties(array $data) {
		foreach($data as $key => $value) {
			$this->$key = $value;
		}
	}

	public function count():int {
		$count = 0;

		foreach($this as $key => $value) {
			$count ++;
		}

		return $count;
	}
}