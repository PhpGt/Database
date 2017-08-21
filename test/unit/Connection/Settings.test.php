<?php
namespace Gt\Database\Connection;

class SettingsTest extends \PHPUnit_Framework_TestCase {

private $properties;

public function setUp()
{
    $this->properties = [
        "baseDirectory" => "/tmp",
        "dataSource" => "test-data-source",
        "database" => "test-database",
        "host" => "test-host",
        "port" => 1234,
        "username" => "test-username",
        "password" => "test-password",
        "tablePrefix" => "test_",
        "connectionName" => "test-connection",
    ];
}

public function testPropertiesSet() {
    $settings = new Settings(
		$this->properties["baseDirectory"],
		$this->properties["dataSource"],
		$this->properties["database"],
		$this->properties["host"],
		$this->properties["port"],
		$this->properties["username"],
		$this->properties["password"],
		$this->properties["tablePrefix"],
		$this->properties["connectionName"]
	);

	$this->assertEquals($this->properties["baseDirectory"], $settings->getBaseDirectory());
	$this->assertEquals($this->properties["dataSource"], $settings->getDataSource());
	$this->assertEquals($this->properties["database"], $settings->getDatabase());
	$this->assertEquals($this->properties["host"], $settings->getHost());
	$this->assertEquals($this->properties["port"], $settings->getPort());
	$this->assertEquals($this->properties["username"], $settings->getUsername());
	$this->assertEquals($this->properties["password"], $settings->getPassword());
	$this->assertEquals($this->properties["tablePrefix"], $settings->getTablePrefix());
	$this->assertEquals($this->properties["connectionName"], $settings->getConnectionName());
}

public function testDefaultConnectionName() {
	$details = [
		"dataSource" => "test-data-source",
		"database" => "test-database",
		"host" => "test-host",
		"port" => 4321,
		"username" => "test-username",
		"password" => "test-password",
	];

	$settings = new Settings(
		"/tmp",
		$details["dataSource"],
		$details["database"],
		$details["host"],
		$details["port"],
		$details["username"],
		$details["password"]
	);

	$this->assertEquals(DefaultSettings::DEFAULT_NAME, $settings->getConnectionName());
}

public function testGetConnectionSettings() {
    $settings = new Settings(
        $this->properties["baseDirectory"],
        $this->properties["dataSource"],
        $this->properties["database"],
        $this->properties["host"],
        $this->properties["port"],
        $this->properties["username"],
        $this->properties["password"],
        $this->properties["tablePrefix"],
        $this->properties["connectionName"]
    );

    $expected = [
        "driver" => $this->properties["dataSource"],
        "host" => $this->properties["host"],
        "port" => $this->properties["port"],
        "database" => $this->properties["database"],
        "username" => $this->properties["username"],
        "password" => $this->properties["password"],
        "charset" => Settings::CHARSET,
        "collation" => Settings::COLLATION,
        "prefix" => $this->properties["tablePrefix"],
        "options" => DefaultSettings::DEFAULT_CONFIG["options"],
    ];

    $actual = $settings->getConnectionSettings();
    $this->assertEquals($expected, $actual);
}

public function testSetConfig() {
    $settings = new Settings(
        $this->properties["baseDirectory"],
        $this->properties["dataSource"],
        $this->properties["database"],
        $this->properties["host"],
        $this->properties["port"],
        $this->properties["username"],
        $this->properties["password"],
        $this->properties["tablePrefix"],
        $this->properties["connectionName"]
    );

    $expected = [
        "optionA" => true,
    ];
    $settings->setConfig([
        "options" => $expected
    ]);

    $actual = $settings->getConnectionSettings();
    $this->assertArrayHasKey("options", $actual);
    $this->assertEquals($expected, $actual["options"]);
}
}#
