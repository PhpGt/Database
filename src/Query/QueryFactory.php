<?php
namespace Gt\Database\Query;

use DirectoryIterator;

class QueryFactory implements QueryFactoryInterface {

const ALLOWED_EXTENSIONS = ["sql", "php"];

/** @var string Absolute path to directory on disk containing query files */
private $directoryOfQueries;

public function __construct(string $directoryOfQueries) {
	$this->directoryOfQueries = $directoryOfQueries;
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
	return new Query();
}

}#