<?php
namespace Gt\Database;

class ClientTest extends \PHPUnit_Framework_TestCase {

public function testInstanceOfInterface() {
	$db = new Client();
	$this->assertInstanceOf("\Gt\Database\ClientInterface", $db);
}

}#