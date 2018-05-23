<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Driver;
use Gt\Database\Result\ResultSet;
use Gt\Database\Result\Row;

class QueryCollection {
	/** @var string */
	protected $directoryPath;
	/** @var QueryFactory */
	protected $queryFactory;

	public function __construct(
		string $directoryPath,
		Driver $driver,
		QueryFactory $queryFactory = null
	) {
		if(is_null($queryFactory)) {
			$queryFactory = new QueryFactory(
				$directoryPath,
				$driver
			);
		}

		$this->directoryPath = $directoryPath;
		$this->queryFactory = $queryFactory;
	}

	public function __call($name, $args) {
		if(isset($args[0]) && is_array($args[0])) {
			$queryArgs = array_merge([$name], $args);
		}
		else {
			$queryArgs = array_merge([$name], [$args]);
		}

		return call_user_func_array([$this, "query"], $queryArgs);
	}

	public function query(
		string $name,
		...$placeholderMap
	):ResultSet {
		$query = $this->queryFactory->create($name);
		return $query->execute($placeholderMap);
	}

	public function insert(
		string $name,
		...$placeholderMap
	):int {
		return (int)$this->query(
			$name,
			...$placeholderMap
		)->lastInsertId();
	}

	public function fetch(
		string $name,
		...$placeholderMap
	):?Row {
		return $this->query(
			$name,
			...$placeholderMap
		)->current();
	}

	public function fetchAll(
		string $name,
		...$placeholderMap
	):ResultSet {
		return $this->query(
			$name,
			...$placeholderMap
		);
	}

	public function update(
		string $name,
		...$placeholderMap
	):int {
		return $this->query(
			$name,
			...$placeholderMap
		)->affectedRows();
	}

	public function delete(
		string $name,
		...$placeholderMap
	):int {
		return $this->query(
			$name,
			...$placeholderMap
		)->affectedRows();
	}

	public function getDirectoryPath():string {
		return $this->directoryPath;
	}
}