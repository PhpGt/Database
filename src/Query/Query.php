<?php
namespace Gt\Database\Query;

abstract class Query extends Builder implements QueryInterface {

/** @var string Absolute path to query file on disk */
private $filePath;

public function __construct(string $filePath) {
	if(!is_file($filePath)) {
		throw new QueryNotFoundException($filePath);
	}

	$this->filePath = $filePath;
	$this->execute();
}

public abstract function execute();

}#