<?php
namespace Gt\Database\Query;

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

	protected function preparePdo():PDO {
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		return $this->connection;
	}

	/**
	 * Certain words are reserved for use by different SQL engines, such as "limit"
	 * and "offset", and can't be used by the driver as bound parameters. This
	 * function returns the SQL for the query after replacing the bound parameters
	 * manually using string replacement.
	 */
	protected function injectSpecialBindings(string $sql, array $bindings):string {
		foreach(self::SPECIAL_BINDINGS as $special) {
			$specialPlaceholder = ":" . $special;

			if(!array_key_exists($special, $bindings)) {
				continue;
			}
			$sql = str_replace($specialPlaceholder, $bindings[$special], $sql);
			unset($bindings[$special]);
		}

		return $sql;
	}

	protected function prepareBindings(array $bindings):array {
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

	protected function ensureParameterCharacter(array $bindings):array {
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

	protected function removeUnusedBindings(array $bindings, string $sql):array {
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

	protected function bindingsEmptyOrNonAssociative(array $bindings):bool {
		return
			$bindings === []
			|| array_keys($bindings) === range(0, count($bindings) - 1);
	}

	/**
	 * $bindings can either be :
	 * 1) An array of individual values for binding to the question mark placeholder,
	 * passed in as variable arguments.
	 * 2) An array containing one single subarray containing key-value-pairs for binding to
	 * named placeholders.
	 *
	 * Due to the use of variable arguments on the Database and QueryCollection classes,
	 * key-value-pair bindings may be double or triple nested.
	 */
	protected function flattenBindings(array $bindings):array {
		if(!isset($bindings[0])) {
			return $bindings;
		}

		$flatArray = [];
		foreach($bindings as $i => $b) {
			while(isset($b[0])
			&& is_array($b[0])) {
				$b = $b[0];
			}

			$flatArray = array_merge($flatArray, $b);
		}

		return $flatArray;
	}
}
