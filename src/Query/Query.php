<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\Driver;
use Gt\Database\Result\ResultSet;

abstract class Query {
	/** @var string Absolute path to query file on disk */
	protected $filePath;
	protected $connection;

	public function __construct(string $filePath, Driver $driver) {
		if(!is_file($filePath)) {
			throw new QueryNotFoundException($filePath);
		}

		$this->filePath = $filePath;
		$this->connection = $driver->getConnection();
	}

	public function getFilePath():string {
		return $this->filePath;
	}

	abstract public function execute(array $bindings = []):ResultSet;

	/**
	 * $bindings can either be :
	 * 1) An array of individual values for binding to the question mark placeholder,
	 * passed in as variable arguments.
	 * 2) An array containing subarrays containing key-value-pairs for binding to
	 * named placeholders.
	 *
	 * Due to the use of variable arguments on the Database and QueryCollection classes,
	 * key-value-pair bindings may be double or triple nested.
	 */
	protected function flattenBindings(array $bindings):array {
		if(!isset($bindings[0])
			|| !is_array($bindings[0])) {
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