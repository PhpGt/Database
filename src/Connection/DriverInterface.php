<?php
namespace Gt\Database\Connection;

use Illuminate\Database\Connection;

interface DriverInterface {

public function getConnection():Connection;

}#