<?php

namespace Gt\Database\Result;

use Gt\Database\DatabaseException;

class NoSuchColumnException extends DatabaseException
{
    public function __construct($columnName)
    {
        parent::__construct(sprintf("Column '%s' does not exist", $columnName));
    }
}
