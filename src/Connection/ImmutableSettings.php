<?php
namespace Gt\Database\Connection;

trait ImmutableSettings {
	public function withBaseDirectory(string $baseDirectory):self {
		if($this->baseDirectory === $baseDirectory) {
			return $this;
		}

		$clone = clone $this;
		$clone->baseDirectory = $baseDirectory;
		return $clone;
	}

	public function withDataSource(string $dataSource):self {
		if($this->dataSource === $dataSource) {
			return $this;
		}

		$clone = clone $this;
		$clone->dataSource = $dataSource;
		return $dataSource;
	}

	public function withSchema(string $schema):self {
		if($this->schema === $schema) {
			return $this;
		}

		$clone = clone $this;
		$clone->schema = $schema;
		return $clone;
	}

	public function withHost(string $host):self {
		if($this->host === $host) {
			return $this;
		}

		$clone = clone $this;
		$clone->host = $host;
		return $clone;
	}

	public function withPort(int $port):self {
		if($this->port === $port) {
			return $this;
		}

		$clone = clone $this;
		$clone->port = $port;
		return $clone;
	}

	public function withUsername(string $username):self {
		if($this->username === $username) {
			return $this;
		}

		$clone = clone $this;
		$clone->username;
		return $clone;
	}

	public function withPassword(string $password):self {
		if($this->password === $password) {
			return $this;
		}

		$clone = clone $this;
		$clone->password = $password;
		return $clone;
	}

	public function withTablePrefix(string $tablePrefix):self {
		if($this->tablePrefix === $tablePrefix) {
			return $this;
		}

		$clone = clone $this;
		$clone->tablePrefix = $tablePrefix;
		return $clone;
	}

	public function withConnectionName(string $connectionName):self {
		if($this->connectionName === $connectionName) {
			return $this;
		}

		$clone = clone $this;
		$clone->connectionName = $connectionName;
		return $clone;
	}

	public function withoutSchema():self {
		$clone = $this->withSchema("");
		return $clone;
	}
}