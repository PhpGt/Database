<?php
namespace Gt\Database\Query;

use DirectoryIterator;

class QueryCollectionFactory {

/** @var string Path to directory containing QueryCollection directories */
private $basePath;
/** @var string The class name to use when creating new QueryCollections */
private $className;

public function __construct(string $basePath = null,
string $className = "\Gt\Database\Query\QueryCollection") {
	if(is_null($basePath)) {
		$basePath = $this->getDefaultBasePath();
	}

	$this->checkClassIsCorrectImplementation($className);
	$this->basePath = $basePath;
	$this->className = $className;
}

public function create(string $name):QueryCollectionInterface {
	$directoryPath = $this->locateDirectory($name);

	if(is_null($directoryPath)) {
		throw new QueryCollectionNotFoundException($name);
	}

	return new $this->className($directoryPath);
}

/**
 * Case-insensitive attempt to match the provided directory name with a
 * directory within the basePath.
 * @param  string $name Name of the QueryCollection
 * @return string       Absolute path to directory
 */
private function locateDirectory(string $name)/* string? */ {
// var_dump($name, $this->basePath);die();
// 	if(!is_dir($this->basePath)) {
// 		return null;
// 	}

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

private function directoryExists(string $name):bool {
	return !is_null($this->locateDirectory());
}

private function getDefaultBasePath():string {
	return realpath(__DIR__ . "/../..");
}

private function checkClassIsCorrectImplementation(string $className) {
	$implementations = class_implements($className);
	if(!in_array(
	"Gt\Database\Query\QueryCollectionInterface", $implementations)) {
		throw new FactoryClassImplementationException($className);
	}
}

}#