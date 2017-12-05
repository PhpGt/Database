<?php

namespace Gt\Database\Result;

use Gt\Database\DatabaseException;

class EmptyResultSetException extends DatabaseException
{
    public function __construct() {
        parent::__construct("Attempted to access row when there were no results");
    }
}
