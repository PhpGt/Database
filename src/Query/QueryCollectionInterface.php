<?php
namespace Gt\Database\Query;

/**
 * Represents a directory on disk containing a collection of queries within
 * a similar area of responsibility.
 */
interface QueryCollectionInterface /* extends QueryBuilder?? */ {

// TODO: PHP 7.1 iterable to allow an array OR \Gt\Database\PlaceholderMap.
public function query(string $name, /*iterable*/ $placeholderValueMap = [])
:QueryInterface;

}#