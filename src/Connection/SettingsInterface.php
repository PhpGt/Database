<?php
namespace Gt\Database\Connection;

interface SettingsInterface {
	public function getBaseDirectory():string;
	public function getDataSource():string;
	public function getSchema():string;
	public function getHost():string;
	public function getPort():int;
	public function getUsername():string;
	public function getPassword():string;
	public function getConnectionName():string;
	public function getTablePrefix():string;
	public function getConnectionString():string;

	public function withBaseDirectory(string $baseDirectory):Settings;
	public function withDataSource(string $dataSource):Settings;
	public function withSchema(string $schema):Settings;
	public function withHost(string $host):Settings;
	public function withPort(int $port):Settings;
	public function withUsername(string $username):Settings;
	public function withPassword(string $password):Settings;
	public function withTablePrefix(string $tablePrefix):Settings;
	public function withConnectionName(string $connectionName):Settings;
	public function withoutSchema():Settings;
}