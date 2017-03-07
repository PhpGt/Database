<?php

namespace Gt\Database\Result;

class NoSuchColumnException extends \Gt\Database\DatabaseException
{
    public function __construct($columnName)
    {
        parent::__construct(sprintf("Column '%s' does not exist", $columnName));
    }
}
