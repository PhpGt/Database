<?php
namespace Gt\Database;

use Gt\Database\Connection\DefaultSettings;
use Gt\Database\Connection\Settings;
use Gt\Database\Query\QueryCollection;
use Gt\Database\Query\QueryCollectionFactory;

class IntegrationTest extends \PHPUnit_Framework_TestCase {

/**
 * @dataProvider \Gt\Database\Test\Helper::queryCollectionPathExistsProvider
 */
public function testDoubleQuery(
	string $queryCollectionName,
	string $path
) {
echo PHP_EOL . "-------------" . PHP_EOL;
	$basePath = dirname($path);
	$settings = new Settings(
		$basePath, Settings::DRIVER_SQLITE, Settings::DATABASE_IN_MEMORY);
	$db = new Client($settings);
	/** @var QueryCollection */
	$queryCollection = $db->queryCollection($queryCollectionName);

	$queryList = [
		"first" => "select :name as `name`",
		"second" => "select :name as `name`, :address as `address`",
		"third" => "select :name as `name`, "
			. "now() as `time`, :address as `address`, :age as age"
	];

	$defaultReplacements = [
		"name" => "Homer Simpson",
		"address" => "742 Evergreen Terrace",
		"age" => 39,
	];

// create the query files according to contents shown above:
	foreach($queryList as $name => $query) {
		$filePath = $path . "/$name.sql";
		file_put_contents($filePath, $query);
	}

// iterate through the queries, checking output is different.
	foreach($queryList as $name => $query) {
		$replacements = [];
		preg_match_all("/:(\w+)/", $query, $placeholderMatches);
		$placeholderList = $placeholderMatches[1];

		foreach ($placeholderList as $placeholder) {
			$replacements[$placeholder] = $defaultReplacements[$placeholder];
		}

		$resultSet = $queryCollection->query($name, $replacements);
		$row = $resultSet->fetchAll()[0];

		var_dump($name, $replacements, $row);

		// $this->assertCount(count($replacements), $row);
		//
		// var_dump($name, $replacements, $row);

		foreach ($replacements as $key => $value) {
		}
	}
}

}#