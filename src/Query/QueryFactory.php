<?php
namespace Gt\Database\Query;

use DirectoryIterator;
use SplFileInfo;
use Gt\Database\Connection\SettingsInterface;

class QueryFactory implements QueryFactoryInterface {

const CLASS_FOR_EXTENSION = [
	"sql" => "\Gt\Database\Query\SqlQuery",
	"php" => "\Gt\Database\Query\PhpQuery",
];

/** @var string Absolute path to directory on disk containing query files */
private $directoryOfQueries;
/** @var \Gt\Database\Connection\SettingsInterface */
private $settings;

public function __construct(
string $directoryOfQueries, SettingsInterface $settings) {
	$this->directoryOfQueries = $directoryOfQueries;
	$this->settings = $settings;
}

public function findQueryFilePath(string $name):string {
	foreach(new DirectoryIterator($this->directoryOfQueries) as $fileInfo) {
		if($fileInfo->isDot()
		|| $fileInfo->isDir()) {
			continue;
		}

		$this->getExtensionIfValid($fileInfo);
		return $fileInfo->getRealPath();
	}

	throw new QueryNotFoundException($this->directoryOfQueries . ", " . $name);
}

public function create(string $name):QueryInterface {
	$queryFilePath = $this->findQueryFilePath($name);
	$queryClass = $this->getQueryClassForFilePath($queryFilePath);

	return new $queryClass($queryFilePath, $this->settings);
}

public function getQueryClassForFilePath(string $filePath) {
	$fileInfo = new SplFileInfo($filePath);
	$ext = $this->getExtensionIfValid($fileInfo);
	return self::CLASS_FOR_EXTENSION[$ext];
}

private function getExtensionIfValid(SplFileInfo $fileInfo) {
	$ext = strtolower($fileInfo->getExtension());

	if(!array_key_exists($ext, self::CLASS_FOR_EXTENSION)) {
		throw new QueryFileExtensionException($ext);
	}

	return $ext;
}

}#