<?php
namespace Gt\Database\Query;

/**
 * Builds Query objects that represent query-files within this factory's
 * base directory.
 */
interface QueryFactoryInterface {

public function findQueryFilePath(string $name):string;

public function create(string $name):QueryInterface;

}#