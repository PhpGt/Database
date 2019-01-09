<?php /** @noinspection PhpIncompatibleReturnTypeInspection */

namespace Gt\Database\Connection;

trait ImmutableSettings {
	public function withBaseDirectory(string $baseDirectory):SettingsInterface {
		if($this->baseDirectory === $baseDirectory) {
			return $this;
		}

		$clone = clone $this;
		$clone->baseDirectory = $baseDirectory;
		return $clone;
	}

	public function withDriver(string $driver):SettingsInterface {
		if($this->driver === $driver) {
			return $this;
		}

		$clone = clone $this;
		$clone->driver = $driver;
		return $clone;
	}

	public function withSchema(string $schema):SettingsInterface {
		if($this->schema === $schema) {
			return $this;
		}

		$clone = clone $this;
		$clone->schema = $schema;
		return $clone;
	}

	public function withHost(string $host):SettingsInterface {
		if($this->host === $host) {
			return $this;
		}

		$clone = clone $this;
		$clone->host = $host;
		return $clone;
	}

	public function withPort(int $port):SettingsInterface {
		if($this->port === $port) {
			return $this;
		}

		$clone = clone $this;
		$clone->port = $port;
		return $clone;
	}

	public function withUsername(string $username):SettingsInterface {
		if($this->username === $username) {
			return $this;
		}

		$clone = clone $this;
		$clone->username = $username;
		return $clone;
	}

	public function withPassword(string $password):SettingsInterface {
		if($this->password === $password) {
			return $this;
		}

		$clone = clone $this;
		$clone->password = $password;
		return $clone;
	}

	public function withConnectionName(string $connectionName):SettingsInterface {
		if($this->connectionName === $connectionName) {
			return $this;
		}

		$clone = clone $this;
		$clone->connectionName = $connectionName;
		return $clone;
	}

	public function withoutSchema():SettingsInterface {
		$clone = $this->withSchema("");
		return $clone;
	}
}