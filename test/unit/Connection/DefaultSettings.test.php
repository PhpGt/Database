<?php
namespace Gt\Database\Connection;

use Gt\Database\Connection\SettingsInterface;

class DefaultSettingsTest extends \PHPUnit_Framework_TestCase {

public function testImplementation() {
	$settings = new DefaultSettings();
	static::assertInstanceOf(SettingsInterface::class, $settings);
}

public function testDefaults() {
	$settings = new DefaultSettings();
	static::assertEquals(
		DefaultSettings::DEFAULT_DATASOURCE,
		$settings->getDataSource()
	);
	static::assertEquals(
		DefaultSettings::DEFAULT_DATABASE,
		$settings->getDatabase()
	);
	static::assertEquals(
		DefaultSettings::DEFAULT_PORT[DefaultSettings::DEFAULT_DATASOURCE],
		$settings->getPort()
	);
	static::assertEquals(
		DefaultSettings::DEFAULT_HOST,
		$settings->getHost()
	);
	static::assertEquals(
		DefaultSettings::DEFAULT_USERNAME,
		$settings->getUsername()
	);
	static::assertEquals(
		DefaultSettings::DEFAULT_PASSWORD,
		$settings->getPassword()
	);
}

/** @dataProvider getDataSources */
public function testDefaultPort(string $dsn, int $port) {
    // NOTE: Have to use a Settings object here as it's not possible to use anything other
    // than the default_datasource otherwise
    $settings = new Settings(
        "/tmp",
        $dsn,
        "test-database");

    static::assertEquals($port, $settings->getPort());
    static::assertEquals([
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

public function getDataSources(): array {
    return [
        [ Settings::DRIVER_MYSQL, 3306 ],
        [ Settings::DRIVER_POSTGRES, 5432],
        [ Settings::DRIVER_SQLSERVER, 1433],
        [ Settings::DRIVER_SQLITE, 0],
    ];
}

}#