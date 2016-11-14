<?php
namespace Gt\Database\Query;

use PDOException;

class SqlQuery extends Query {

public function prepare():QueryInterface {
	try {

	}
	catch(PDOException $exception) {
		throw new PreparedQueryException(null, 0, $exception);
	}

	return $this;
}

}#