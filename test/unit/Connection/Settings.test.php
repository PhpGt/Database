<?php
namespace Gt\Database\Connection;

class SettingsTest extends \PHPUnit_Framework_TestCase {

public function testPropertiesSet() {
	$details = [
		"baseDirectory" => "/tmp",
		"dataSource" => "test-data-source",
		"database" => "test-database",
		"host" => "test-host",
		"username" => "test-username",
		"password" => "test-password",
		"tablePrefix" => "test_",
		"connectionName" => "test-connection",
	];

	$settings = new Settings(
		$details["baseDirectory"],
		$details["dataSource"],
		$details["database"],
		$details["host"],
		$details["username"],
		$details["password"],
		$details["tablePrefix"],
		$details["connectionName"]
	);

	$this->assertEquals(
		$details["baseDirectory"], $settings->getBaseDirectory());
	$this->assertEquals($details["dataSource"], $settings->getDataSource());
	$this->assertEquals($details["database"], $settings->getDatabase());
	$this->assertEquals($details["host"], $settings->getHost());
	$this->assertEquals($details["username"], $settings->getUsername());
	$this->assertEquals($details["password"], $settings->getPassword());
	$this->assertEquals($details["tablePrefix"], $settings->getTablePrefix());
	$this->assertEquals(
		$details["connectionName"], $settings->getConnectionName());
}

public function testDefaultConnectionName() {
	$details = [
		"dataSource" => "test-data-source",
		"database" => "test-database",
		"host" => "test-host",
		"username" => "test-username",
		"password" => "test-password",
	];

	$settings = new Settings(
		$details["dataSource"],
		$details["database"],
		$details["host"],
		$details["username"],
		$details["password"]
	);

	$this->assertEquals(
		DefaultSettings::DEFAULT_NAME, $settings->getConnectionName());
}

}#