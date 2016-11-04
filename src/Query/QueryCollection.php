<?php
namespace Gt\Database\Query;

use DirectoryIterator;

class QueryCollection implements QueryCollectionInterface {

const ALLOWED_EXTENSIONS = ["sql", "php"];

/** @var string */
private $directoryPath;

public function __construct(string $directoryPath) {
	$this->directoryPath = $directoryPath;
}

public function query(string $name, array $placeholderValueMap = []) {
	$filePath = $this->findQueryFilePath($name);
}

private function findQueryFilePath(string $name):string {
	foreach(new DirectoryIterator($this->directoryPath) as $fileInfo) {
		if($fileInfo->isDot()
		|| $fileInfo->isDir()) {
			continue;
		}

		$ext = strtolower(fileInfo-getExtension());
		if(!in_array($ext, self::ALLOWED_EXTENSIONS)) {
			continue;
		}

		return $this->getRealPath();
	}

	throw new QueryNotFoundException($this->collectionName . "::" . $name);
}

}#