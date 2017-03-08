<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Driver;
use Gt\Database\Result\ResultSet;
use Gt\Database\Result\Row;

class QueryCollection {

/** @var string */
private $directoryPath;
/** @var QueryFactory */
private $queryFactory;

public function __construct(
string $directoryPath, Driver $driver, QueryFactory $queryFactory = null) {
	if(is_null($queryFactory)) {
		$queryFactory = new QueryFactory($directoryPath, $driver);
	}

	$this->directoryPath = $directoryPath;
	$this->queryFactory = $queryFactory;
}

public function __call($name, $args) {
	$queryArgs = [];

	if(isset($args[0]) && is_array($args[0])) {
		$queryArgs = array_merge([$name], $args);
	}
	else {
		$queryArgs = array_merge([$name], [$args]);
	}

	return call_user_func_array([$this, "query"], $queryArgs);
}

// TODO: PHP 7.1 iterable, to allow Gt\Database\Gt\Database\PlaceholderMap
public function query(
string $name, /*iterable*/array $placeholderMap = []):ResultSet {
	/** @var Query */
	$query = $this->queryFactory->create($name);
	return $query->execute($placeholderMap);
}

public function create(
    string $name, /*iterable*/array $placeholderMap = []):string {
    return $this->query($name, $placeholderMap)->getLastInsertId();
}

/** @return Row|null */
public function retrieve(
    string $name, /*iterable*/array $placeholderMap = []) {
    return $this->query($name, $placeholderMap)->fetch();
}

public function retrieveAll(
    string $name, /*iterable*/array $placeholderMap = []) {
    return $this->query($name, $placeholderMap)->fetchAll();
}

public function update(
    string $name, /*iterable*/array $placeholderMap = []):int {
    return $this->query($name, $placeholderMap)->getAffectedRows();
}

public function delete(
    string $name, /*iterable*/array $placeholderMap = []):int {
    return $this->query($name, $placeholderMap)->getAffectedRows();
}

public function getDirectoryPath():string {
	return $this->directoryPath;
}

}#
