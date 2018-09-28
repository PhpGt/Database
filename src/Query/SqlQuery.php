<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Connection;
use PDO;
use PDOException;
use PDOStatement;
use DateTime;
use Gt\Database\Result\ResultSet;

class SqlQuery extends Query {
	const SPECIAL_BINDINGS = [
		"limit",
		"offset",
		"groupBy",
		"orderBy",
	];

	public function getSql(array $bindings = []):string {
		$sql = file_get_contents($this->getFilePath());
		$sql = $this->injectSpecialBindings($sql, $bindings);

		return $sql;
	}

	public function execute(array $bindings = []):ResultSet {
		$bindings = $this->flattenBindings($bindings);

		$pdo = $this->preparePdo();
		$sql = $this->getSql($bindings);
		$statement = $this->prepareStatement($pdo, $sql);
		$preparedBindings = $this->prepareBindings($bindings);
		$preparedBindings = $this->ensureParameterCharacter($preparedBindings);
		$preparedBindings = $this->removeUnusedBindings($preparedBindings, $sql);
		$lastInsertId = null;

		try {
			$statement->execute($preparedBindings);
			$lastInsertId = $pdo->lastInsertId();
		}
		catch(PDOException $exception) {
			throw new PreparedStatementException(null, 0, $exception);
		}

		return new ResultSet($statement, $lastInsertId);
	}

	public function prepareStatement(PDO $pdo, string $sql):PDOStatement {
		try {
			$statement = $pdo->prepare($sql);

			return $statement;
		}
		catch(PDOException $exception) {
			throw new PreparedStatementException(null, 0, $exception);
		}
	}

	/**
	 * Certain words are reserved for use by different SQL engines, such as "limit"
	 * and "offset", and can't be used by the driver as bound parameters. This
	 * function returns the SQL for the query after replacing the bound parameters
	 * manually using string replacement.
	 */
	public function injectSpecialBindings(
		string $sql,
		array $bindings
	):string {
		foreach(self::SPECIAL_BINDINGS as $special) {
			$specialPlaceholder = ":" . $special;

			if(!array_key_exists($special, $bindings)) {
				continue;
			}

			$replacement = $this->escapeSpecialBinding(
				$bindings[$special],
				$special
			);

			$sql = str_replace(
				$specialPlaceholder,
				$replacement,
				$sql
			);
			unset($bindings[$special]);
		}

		return $sql;
	}

	public function prepareBindings(array $bindings):array {
		foreach($bindings as $key => $value) {
			if(is_bool($value)) {
				$bindings[$key] = (int)$value;
			}
			if($value instanceof DateTime) {
				$bindings[$key] = $value->format("Y-m-d H:i:s");
			}
		}

		return $bindings;
	}

	public function ensureParameterCharacter(array $bindings):array {
		if($this->bindingsEmptyOrNonAssociative($bindings)) {
			return $bindings;
		}

		foreach($bindings as $key => $value) {
			if(substr($key, 0, 1) !== ":") {
				$bindings[":" . $key] = $value;
				unset($bindings[$key]);
			}
		}

		return $bindings;
	}

	public function removeUnusedBindings(array $bindings, string $sql):array {
		if($this->bindingsEmptyOrNonAssociative($bindings)) {
			return $bindings;
		}

		foreach($bindings as $key => $value) {
			if(!preg_match("/{$key}(\W|\$)/", $sql)) {
				unset($bindings[$key]);
			}
		}

		return $bindings;
	}

	public function bindingsEmptyOrNonAssociative(array $bindings):bool {
		return
			$bindings === []
			|| array_keys($bindings) === range(
				0,
				count($bindings) - 1);
	}

	protected function preparePdo():PDO {
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $this->connection;
	}

	protected function escapeSpecialBinding(
		string $value,
		string $type
	):string {
		$value = preg_replace(
			"/[^0-9a-z,'\"`\s]/i",
			"",
			$value
		);

// TODO: In v2 we will properly parse the different parts of the special bindings.
// See https://github.com/PhpGt/Database/issues/117
		switch($type) {
		// [GROUP BY {col_name | expr | position}, ... [WITH ROLLUP]]
		case "groupBy":
			break;

		// [ORDER BY {col_name | expr | position}
		case "orderBy":
			break;

		// [LIMIT {[offset,] row_count | row_count OFFSET offset}]
		case "limit":
			break;

		// [LIMIT {[offset,] row_count | row_count OFFSET offset}]
		case "offset":
			break;
		}

		return (string)$value;
	}
}
