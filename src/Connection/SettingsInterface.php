<?php
namespace Gt\Database\Connection;

interface SettingsInterface {

const DEFAULT_NAME = "default";

const DRIVER_MYSQL = "mysql";
const DRIVER_POSTGRES = "pgsql";
const DRIVER_SQLITE = "sqlite";
const DRIVER_SQLSERVER = "dblib";

public function getDataSource():string;
public function getDatabase():string;
public function getHostname():string;
public function getUsername():string;
public function getPassword():string;
public function getConnectionName():string;

}#