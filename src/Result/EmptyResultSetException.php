<?php

namespace Gt\Database\Result;

class EmptyResultSetException extends \Gt\Database\DatabaseException
{
    public function __construct()
    {
        parent::__construct("Attempted to access row when there were no results");
    }
}
