<?php
namespace Gt\Database\Connection;

class SettingsTest extends \PHPUnit_Framework_TestCase {

public function testPropertiesSet() {
	$details = [
		"dataSource" => "test-data-source",
		"database" => "test-database",
		"hostname" => "test-hostname",
		"username" => "test-username",
		"password" => "test-password",
		"connectionName" => "test-connection",
	];

	$settings = new Settings(
		$details["dataSource"],
		$details["database"],
		$details["hostname"],
		$details["username"],
		$details["password"],
		$details["connectionName"]
	);

	$this->assertEquals($details["dataSource"], $settings->getDataSource());
	$this->assertEquals($details["database"], $settings->getDatabase());
	$this->assertEquals($details["hostname"], $settings->getHostname());
	$this->assertEquals($details["username"], $settings->getUsername());
	$this->assertEquals($details["password"], $settings->getPassword());
	$this->assertEquals(
		$details["connectionName"], $settings->getConnectionName());
}

public function testDefaultConnectionName() {
	$details = [
		"dataSource" => "test-data-source",
		"database" => "test-database",
		"hostname" => "test-hostname",
		"username" => "test-username",
		"password" => "test-password",
	];

	$settings = new Settings(
		$details["dataSource"],
		$details["database"],
		$details["hostname"],
		$details["username"],
		$details["password"]
	);

	$this->assertEquals(
		Settings::DEFAULT_NAME, $settings->getConnectionName());
}

}#