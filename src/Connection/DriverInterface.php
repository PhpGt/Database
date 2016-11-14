<?php
namespace Gt\Database\Connection;

interface DriverInterface {

public function connect();
public function getConnection():ConnectionInterface;

}#