<?php
namespace Gt\Database;

use Gt\Database\Query\QueryCollectionInterface;
use Gt\Database\Query\QueryCollectionFactory;
use Gt\Database\Connection\Settings;
use ArrayAccess;

interface DatabaseClientInterface extends ArrayAccess {

public function __construct(Settings $connectionSettings = null,
QueryCollectionFactory $tableCollectionFactory = null);

/**
 * Synonym for ArrayAccess::offsetGet
 */
public function queryCollection(string $name):QueryCollectionInterface;

}#