<?php
namespace Gt\Database\Query;

use DirectoryIterator;
use Gt\Database\Connection\Driver;

class QueryCollectionFactory {

/** @var Driver */
protected $driver;
/** @var string */
protected $basePath;
/** @var array */
protected $queryCollectionCache = [];

public function __construct(Driver $driver) {
	$this->driver = $driver;
	$this->basePath = $this->driver->getBaseDirectory();
}

public function create(string $name):QueryCollection {
	if(!isset($this->queryCollectionCache[$name])) {
		$directoryPath = $this->locateDirectory($name);

		if(is_null($directoryPath)) {
			throw new QueryCollectionNotFoundException($name);
		}

		$this->queryCollectionCache[$name] = new QueryCollection(
			$directoryPath,
			$this->driver
		);
	}

	return $this->queryCollectionCache[$name];

}

public function directoryExists(string $name):bool {
	return !is_null($this->locateDirectory($name));
}

/**
 * Case-insensitive attempt to match the provided directory name with a
 * directory within the basePath.
 * @param  string $name Name of the QueryCollection
 * @return string       Absolute path to directory
 */
protected function locateDirectory(string $name)/* :?string */ {
	foreach (new DirectoryIterator($this->basePath) as $fileInfo) {
		if($fileInfo->isDot()
		|| !$fileInfo->isDir()) {
			continue;
		}

		$basename = $fileInfo->getBasename();
		if(strtolower($name) === strtolower($basename)) {
			return $fileInfo->getRealPath();
		}
	}

	return null;
}

protected function getDefaultBasePath():string {
	return getcwd();
}

}#