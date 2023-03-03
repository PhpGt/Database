<?php
namespace Gt\Database\Result;

use Countable;
use PDO;
use Iterator;
use PDOStatement;

/**
 * @property int $length Number of rows represented, synonym of
 * count and getLength
 *
 * @implements Iterator<int, Row>
 */
class ResultSet implements Iterator, Countable {
	protected ?Row $currentRow;
	protected int $rowIndex;
	protected int $iteratorIndex;

	public function __construct(
		protected PDOStatement $statement,
		protected ?string $insertId = null
	) {
		$this->iteratorIndex = 0;
	}

	public function affectedRows():int {
		return $this->statement->rowCount();
	}

	public function lastInsertId():string {
		return $this->insertId;
	}

	public function fetch():?Row {
		$data = $this->statement->fetch(
			PDO::FETCH_ASSOC
		);

		if(!isset($this->rowIndex)) {
			$this->rowIndex = 0;
		}
		else {
			$this->rowIndex++;
		}

		if(empty($data)) {
			$this->currentRow = null;
		}
		else {
			$this->currentRow = new Row($data);
		}

		return $this->currentRow;
	}

	/** @return array<Row> */
	public function fetchAll():array {
		$this->statement->execute();
		$this->rowIndex = 0;
		$this->iteratorIndex = 0;

		$data = [];

		while($row = $this->fetch()) {
			$data [] = $row;
		}

		return $data;
	}

	protected function fetchUpToIteratorIndex():void {
		while(!isset($this->rowIndex)
		|| $this->rowIndex < $this->iteratorIndex) {
			$this->fetch();
		}
	}

	/** @return array<int, array<string, string>> */
	public function asArray():array {
		return array_map(function($element) {
				return $element->asArray();
			},
			$this->fetchAll()
		);
	}

	public function rewind():void {
		$this->statement->execute();
		$this->currentRow = null;
		unset($this->rowIndex);
		$this->iteratorIndex = 0;
	}

	public function current():?Row {
		$this->fetchUpToIteratorIndex();
		return $this->currentRow;
	}

	public function key():int {
		return $this->iteratorIndex;
	}

	public function next():void {
		$this->iteratorIndex++;
	}

	public function valid():bool {
		return !empty($this->current());
	}

	public function count():int {
		$currentIteratorIndex = $this->iteratorIndex;
		$count = count($this->fetchAll());
		$this->rewind();
		$this->iteratorIndex = $currentIteratorIndex;
		return $count;
	}
}
