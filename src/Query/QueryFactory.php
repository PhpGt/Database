<?php
namespace Gt\Database\Query;

use DirectoryIterator;

class QueryFactory implements QueryFactoryInterface {

const ALLOWED_EXTENSIONS = ["sql", "php"];

/** @var string Absolute path to directory on disk containing query files */
private $directoryOfQueries;
/** @var string Which class to use when creating Queries. */
private $className;

public function __construct(string $directoryOfQueries,
string $className = Query::class) {
	$this->checkClassIsCorrectImplementation($className);

	$this->directoryOfQueries = $directoryOfQueries;
	$this->className = $className;
}

public function findQueryFilePath(string $name):string {
	foreach(new DirectoryIterator($this->directoryOfQueries) as $fileInfo) {
		if($fileInfo->isDot()
		|| $fileInfo->isDir()) {
			continue;
		}

		$ext = strtolower($fileInfo->getExtension());
		if(!in_array($ext, self::ALLOWED_EXTENSIONS)) {
			continue;
		}

		return $fileInfo->getRealPath();
	}

	throw new QueryNotFoundException($this->directoryOfQueries . ", " . $name);
}

public function create(string $name):QueryInterface {
	return new $this->className();
}

private function checkClassIsCorrectImplementation($className) {
	$implementations = class_implements($className);
	if(!in_array("Gt\Database\Query\QueryInterface", $implementations)) {
		throw new FactoryClassImplementationException($className);
	}
}

}#