<?php
namespace Gt\Database\Query;

use Gt\Database\Connection\SettingsInterface;

abstract class Query extends Builder implements QueryInterface {

/** @var string Absolute path to query file on disk */
private $filePath;
/** @var \Gt\Database\Connection\SettingsInterface */
private $settings;

public function __construct(string $filePath, SettingsInterface $settings) {
	if(!is_file($filePath)) {
		throw new QueryNotFoundException($filePath);
	}

	$this->filePath = $filePath;
	$this->settings = $settings;
	$this->execute();
}

public abstract function execute();

public function getFilePath():string {
	return $this->filePath;
}

}#