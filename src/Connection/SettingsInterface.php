<?php
namespace Gt\Database\Connection;

interface SettingsInterface {
	public function getBaseDirectory():string;
	public function getDriver():string;
	public function getSchema():string;
	public function getHost():string;
	public function getPort():int;
	public function getUsername():string;
	public function getPassword():string;
	public function getConnectionName():string;
	public function getConnectionString():string;

	public function withBaseDirectory(string $baseDirectory):SettingsInterface;
	public function withDriver(string $driver):SettingsInterface;
	public function withSchema(string $schema):SettingsInterface;
	public function withHost(string $host):SettingsInterface;
	public function withPort(int $port):SettingsInterface;
	public function withUsername(string $username):SettingsInterface;
	public function withPassword(string $password):SettingsInterface;
	public function withConnectionName(string $connectionName):SettingsInterface;
	public function withoutSchema():SettingsInterface;
}