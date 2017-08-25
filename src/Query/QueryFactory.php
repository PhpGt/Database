<?php
namespace Gt\Database\Query;

use SplFileInfo;
use DirectoryIterator;
use Exception;
use InvalidArgumentException;
use Gt\Database\Connection\Driver;
use Gt\Database\Connection\ConnectionNotConfiguredException;

class QueryFactory {

const CLASS_FOR_EXTENSION = [
	"sql" => "\Gt\Database\Query\SqlQuery",
	"php" => "\Gt\Database\Query\PhpQuery",
];

/** @var string Absolute path to directory on disk containing query files */
protected $directoryOfQueries;
/** @var \Gt\Database\Connection\Driver */
protected $driver;

public function __construct(
	string $directoryOfQueries,
	Driver $driver
) {
	$this->directoryOfQueries = $directoryOfQueries;
	$this->driver = $driver;
}

public function findQueryFilePath(string $name):string {
	foreach(new DirectoryIterator($this->directoryOfQueries) as $fileInfo) {
		if($fileInfo->isDot()
		|| $fileInfo->isDir()) {
			continue;
		}

		$this->getExtensionIfValid($fileInfo);
		$fileNameNoExtension = strtok($fileInfo->getFilename(), ".");
		if($fileNameNoExtension !== $name) {
			continue;
		}

		return $fileInfo->getRealPath();
	}

	throw new QueryNotFoundException($this->directoryOfQueries . ", " . $name);
}

public function create(string $name):Query {
	try {
		$queryFilePath = $this->findQueryFilePath($name);
		$queryClass = $this->getQueryClassForFilePath($queryFilePath);
		$query = new $queryClass($queryFilePath, $this->driver);
		return $query;
	}
	catch(InvalidArgumentException $exception) {
		$this->throwCorrectException($exception);
	}
}

public function getQueryClassForFilePath(string $filePath) {
	$fileInfo = new SplFileInfo($filePath);
	$ext = $this->getExtensionIfValid($fileInfo);
	return self::CLASS_FOR_EXTENSION[$ext];
}

protected function getExtensionIfValid(SplFileInfo $fileInfo) {
	$ext = strtolower($fileInfo->getExtension());

	if(!array_key_exists($ext, self::CLASS_FOR_EXTENSION)) {
		throw new QueryFileExtensionException($ext);
	}

	return $ext;
}

protected function throwCorrectException(Exception $exception) {
	$message = $exception->getMessage();

	switch(get_class($exception)) {
	case InvalidArgumentException::class:
		$matches = [];
		if(1 !== preg_match(
		"/Database \[(.+)\] not configured/", $message, $matches)) {
			throw $exception;
		}

		$connectionName = $matches[1];
		throw new ConnectionNotConfiguredException($connectionName);
		break;

	default:
		throw $exception;
	}
}

}#