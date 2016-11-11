<?php
namespace Gt\Database\Connection;

class DefaultSettingsTest extends \PHPUnit_Framework_TestCase {

public function testImplementation() {
	$settings = new DefaultSettings();
	$this->assertInstanceOf(
		"\Gt\Database\Connection\SettingsInterface",
		$settings
	);
}

public function testDefaults() {
	$settings = new DefaultSettings();
	$this->assertEquals(
		DefaultSettings::DEFAULT_DATASOURCE,
		$settings->getDataSource()
	);
	$this->assertEquals(
		DefaultSettings::DEFAULT_DATABASE,
		$settings->getDatabase()
	);
	$this->assertEquals(
		DefaultSettings::DEFAULT_HOSTNAME,
		$settings->getHostname()
	);
	$this->assertEquals(
		DefaultSettings::DEFAULT_USERNAME,
		$settings->getUsername()
	);
	$this->assertEquals(
		DefaultSettings::DEFAULT_PASSWORD,
		$settings->getPassword()
	);
}

}#