<?php
namespace Gt\Database\Query;

use PDO;
use PDOException;
use PDOStatement;
use Gt\Database\Result\ResultSet;
use Illuminate\Database\Connection;

class SqlQuery extends Query {

public function getSql():string {
	return file_get_contents($this->getFilePath());
}

public function execute(array $bindings = []):PDOStatement {
	$pdo = $this->preparePdo();
	$statement = $this->prepareStatement($pdo, $this->getSql());
	$preparedBindings = $this->connection->prepareBindings($bindings);

	try {
		$statement->execute($preparedBindings);
	}
	catch(PDOException $exception) {
		throw new PreparedStatementException(null, 0, $exception);
	}

	return $statement;
}

public function getResultSet(PDOStatement $statement):ResultSetInterface {
	return new ResultSet($statement);
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

}#