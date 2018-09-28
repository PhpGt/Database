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
		
		if (!strstr($filePath,'.sql')) {
			throw new QueryFileTypeException($filePath);
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
	 * key-value-pair bindings may be double or triple nested at this point.
	 */
	protected function flattenBindings(array $bindings):array {
		if(!isset($bindings[0])) {
			return $bindings;
		}

		if(is_object($bindings[0])
		&& method_exists($bindings[0], "toArray")) {
			$bindings = array_map(function($element) {
				if(method_exists($element, "toArray")) {
					return $element->toArray();
				}

				return $element;
			}, $bindings);
		}

		$flatArray = [];
		foreach($bindings as $i => $b) {
			while(isset($b[0])
			&& is_array($b[0])) {
				$merged = [];
				foreach($b as $innerKey => $innerValue) {
					$merged = array_merge(
						$merged,
						$innerValue
					);
				}

				$b = $merged;
			}

			if(!is_array($b)) {
				$b = [$b];
			}

			$flatArray = array_merge($flatArray, $b);
		}

		return $flatArray;
	}
}
