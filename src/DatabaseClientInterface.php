<?php
namespace Gt\Database;

use Gt\Database\Query\QueryCollectionInterface;
use Gt\Database\Query\QueryCollectionFactory;
use Gt\Database\Connection\Settings;
use ArrayAccess;

interface DatabaseClientInterface extends ArrayAccess {

/**
 * Synonym for ArrayAccess::offsetGet
 */
public function queryCollection(string $name):QueryCollectionInterface;

}#