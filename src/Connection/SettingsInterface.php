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
}