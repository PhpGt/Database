<?php
namespace Gt\Database\Query;

/**
 * Represents a directory on disk containing a collection of queries within
 * a similar area of responsibility.
 */
interface QueryCollectionInterface /* extends QueryBuilder?? */ {

public function __construct(string $directoryPath);

public function query(string $name, array $placeholderValueMap = []);

}#