<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Driver;
use Gt\Database\Fetchable;
use Gt\Database\Result\ResultSet;

class QueryCollection {
	use Fetchable;

	protected string $directoryPath;
	protected QueryFactory $queryFactory;

	public function __construct(
		string $directoryPath,
		Driver $driver,
		QueryFactory $queryFactory = null
	) {
		$this->directoryPath = $directoryPath;
		$this->queryFactory = $queryFactory ?? new QueryFactory(
			$directoryPath,
			$driver
		);
	}

	/** @param array<mixed> $args */
	public function __call(string $name, array $args):ResultSet {
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
		mixed...$placeholderMap
	):ResultSet {
		$query = $this->queryFactory->create($name);
		return $query->execute($placeholderMap);
	}

	public function insert(
		string $name,
		mixed...$placeholderMap
	):int {
		return (int)$this->query(
			$name,
			...$placeholderMap
		)->lastInsertId();
	}

	public function update(
		string $name,
		mixed...$placeholderMap
	):int {
		return $this->query(
			$name,
			...$placeholderMap
		)->affectedRows();
	}

	public function delete(
		string $name,
		mixed...$placeholderMap
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
