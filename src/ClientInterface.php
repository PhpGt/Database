<?php
namespace Gt\Database;

interface ClientInterface extends ConnectionInterface, ArrayAccess {

/**
 * Synonym for ArrayAccess::offsetGet
 */
public function tableCollection(string $name):TableCollectionInterface;

}#