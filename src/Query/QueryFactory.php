<?php
namespace Gt\Database\Query;

class QueryFactory implements QueryFactoryInterface {

const ALLOWED_EXTENSIONS = ["sql", "php"];

/** @var string Absolute path to directory on disk containing query files */
private $directoryPath;

public function __construct(string $directoryPath) {
	$this->directoryPath = $directoryPath;
}

public function findQueryFilePath(string $name):string {
	foreach(new DirectoryIterator($this->directoryPath) as $fileInfo) {
		if($fileInfo->isDot()
		|| $fileInfo->isDir()) {
			continue;
		}

		$ext = strtolower($fileInfo->getExtension());
		if(!in_array($ext, self::ALLOWED_EXTENSIONS)) {
			continue;
		}

		return $this->getRealPath();
	}

	throw new QueryNotFoundException($this->collectionName . "::" . $name);
}

public function create(string $name):QueryInterface {
	// TODO.
}

}#