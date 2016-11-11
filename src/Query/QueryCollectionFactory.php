<?php
namespace Gt\Database\Query;

use DirectoryIterator;

class QueryCollectionFactory {

/** @var string Path to directory containing QueryCollection directories */
private $basePath;

public function __construct(string $basePath = null) {
	if(is_null($basePath)) {
		$basePath = $this->getDefaultBasePath();
	}

	$this->basePath = $basePath;
}

public function create(string $name):QueryCollectionInterface {
	$directoryPath = $this->locateDirectory($name);

	if(is_null($directoryPath)) {
		throw new QueryCollectionNotFoundException($name);
	}

	return new QueryCollection($directoryPath);
}

public function directoryExists(string $name):bool {
	$thing = !is_null($this->locateDirectory($name));
	return !is_null($this->locateDirectory($name));
}

/**
 * Case-insensitive attempt to match the provided directory name with a
 * directory within the basePath.
 * @param  string $name Name of the QueryCollection
 * @return string       Absolute path to directory
 */
private function locateDirectory(string $name)/* :string? */ {
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

private function getDefaultBasePath():string {
	return getcwd();
}

}#