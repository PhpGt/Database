<?php
namespace Gt\Database\Test;

require(__DIR__ . "/../../vendor/autoload.php");

class Helper {

const COUNT_PATH_PROVIDER = 10;

public static function getTmpDir() {
	return implode("/", [
		sys_get_temp_dir(),
		"phpgt",
		"database",
	]);
}

public static function deleteTmpDir() {
	exec("rm -rf " . self::getTmpDir());
}

public function queryPathExistsProvider() {
	return $this->queryPathProvider(true);
}

public function queryPathNotExistsProvider() {
	return $this->queryPathProvider(false);
}

private function queryPathProvider(bool $exists) {
	$data = [];

	foreach($this->queryCollectionPathProvider(true) as $qcName => $qcData) {
		$queryCollectionPath = $qcData[1];

		$queryName = uniqid("query");
		$filename = $queryName . ".sql";
		$filePath = implode(DIRECTORY_SEPARATOR, [
			$queryCollectionPath,
			$filename,
		]);

		if($exists) {
			touch($filePath);
		}

		$data []= [
			$queryName,
			$queryCollectionPath,
		];
	}

	return $data;
}

public function queryCollectionPathExistsProvider() {
	return $this->queryCollectionPathProvider(true);
}

public function queryCollectionPathNotExistsProvider() {
	return $this->queryCollectionPathProvider(false);
}

private function queryCollectionPathProvider(bool $exists) {
	$data = [];

	for($i = 0; $i < self::COUNT_PATH_PROVIDER; ++$i) {
		$name = uniqid();
		$path = self::getTmpDir() . "/query/" . $name;

		if($exists) {
			mkdir($path, 0775, true);
		}

		$data []= [
			$name,
			$path
		];
	}

	return $data;
}

}#