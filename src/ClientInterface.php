<?php
namespace Gt\Database;

use ArrayAccess;

interface ClientInterface extends ArrayAccess {

/**
 * Synonym for ArrayAccess::offsetGet
 */
public function tableCollection(string $name):TableCollectionInterface;

}#