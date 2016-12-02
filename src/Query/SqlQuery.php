<?php
namespace Gt\Database\Query;

use PDO;
use PDOException;
use PDOStatement;
use Gt\Database\Result\ResultSet;
use Illuminate\Database\Connection;

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
	$pdo = $this->preparePdo();
	$statement = $this->prepareStatement($pdo, $this->getSql($bindings));
	$preparedBindings = $this->connection->prepareBindings($bindings);
	$preparedBindings = $this->ensureParameterCharacter($preparedBindings);
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

private function preparePdo() {
	$pdo = $this->connection->getPdo();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	return $pdo;
}

private function injectSpecialBindings(string $sql, array $bindings) {
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

private function ensureParameterCharacter(array $bindings) {
	foreach($bindings as $key => $value) {
		if(substr($key, 0, 1) !== ":") {
			$bindings[":" . $key] = $value;
			unset($bindings[$key]);
		}
	}

	return $bindings;
}

}#