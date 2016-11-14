<?php
namespace Gt\Database\Query;

use DirectoryIterator;
use SplFileInfo;
use Gt\Database\Connection\Driver;
use Gt\Database\Connection\DriverInterface;
use Gt\Database\Connection\SettingsInterface;

class QueryFactory implements QueryFactoryInterface {

const CLASS_FOR_EXTENSION = [
	"sql" => "\Gt\Database\Query\SqlQuery",
	"php" => "\Gt\Database\Query\PhpQuery",
];

/** @var string Absolute path to directory on disk containing query files */
private $directoryOfQueries;
/** @var \Gt\Database\Connection\DriverInterface */
private $driver;

public function __construct(
string $directoryOfQueries, SettingsInterface $settings) {
	$this->directoryOfQueries = $directoryOfQueries;
	$this->driver = new Driver($settings);
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

// TODO: PHP 7.1 iterable, to allow Gt\Database\Gt\Database\PlaceholderMap
public function create(
string $name, /*iterable*/array $placeholderMap = []):QueryInterface {
	$queryFilePath = $this->findQueryFilePath($name);
	$queryClass = $this->getQueryClassForFilePath($queryFilePath);
	$query = new $queryClass($queryFilePath, $this->driver);
	$query->prepare($placeholderMap);
	return $query;
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