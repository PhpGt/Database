<?php
namespace Gt\Database\Connection;

class DefaultSettingsTest extends \PHPUnit_Framework_TestCase {

public function testImplementation() {
	$settings = new DefaultSettings();
	$this->assertInstanceOf(
		"\\Gt\\Database\\Connection\\SettingsInterface",
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
		DefaultSettings::DEFAULT_PORT[DefaultSettings::DEFAULT_DATASOURCE],
		$settings->getPort()
	);
	$this->assertEquals(
		DefaultSettings::DEFAULT_HOST,
		$settings->getHost()
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

/** @dataProvider getDatasources */
public function testDefaultPort(string $dsn, int $port) {
    // NOTE: Have to use a Settings object here as it's not possible to use anything other
    // than the default_datasource otherwise
    $settings = new Settings(
        "/tmp",
        $dsn,
        "test-database");

    $this->assertEquals($port, $settings->getPort());
    $this->assertEquals([
        "driver" => $dsn,
        "host" => DefaultSettings::DEFAULT_HOST,
        "port" => $port,
        "database" => "test-database",
        "username" => DefaultSettings::DEFAULT_USERNAME,
        "password" => DefaultSettings::DEFAULT_PASSWORD,
        "charset" => DefaultSettings::CHARSET,
        "collation" => DefaultSettings::COLLATION,
        "prefix" => "",
        "options" => DefaultSettings::DEFAULT_CONFIG["options"],
    ], $settings->getConnectionSettings());
}

public function getDatasources(): array
{
    return [
        [ Settings::DRIVER_MYSQL, 3306 ],
        [ Settings::DRIVER_POSTGRES, 5432],
        [ Settings::DRIVER_SQLSERVER, 1433],
        [ Settings::DRIVER_SQLITE, 0],
    ];
}
}#
