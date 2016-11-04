<?php
namespace Gt\Database\Test;

require(__DIR__ . "/../../vendor/autoload.php");

class Helper {

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
	$count = 10;

	for($i = 0; $i < $count; ++$i) {
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