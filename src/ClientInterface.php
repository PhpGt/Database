<?php
namespace Gt\Database;

use Gt\Database\Entity\TableCollectionInterface;
use Gt\Database\Entity\TableCollectionFactory;
use Gt\Database\Connection\Settings;
use ArrayAccess;

interface ClientInterface extends ArrayAccess {

public function __construct(Settings $connectionSettings = null,
TableCollectionFactory $tableCollectionFactory = null);

/**
 * Synonym for ArrayAccess::offsetGet
 */
public function tableCollection(string $name):TableCollectionInterface;

}#