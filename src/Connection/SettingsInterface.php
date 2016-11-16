<?php
namespace Gt\Database\Connection;

interface SettingsInterface {

public function getDataSource():string;
public function getDatabase():string;
public function getHostname():string;
public function getUsername():string;
public function getPassword():string;
public function getConnectionName():string;
public function getTablePrefix():string;

}#