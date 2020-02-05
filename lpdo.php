<?php

class LPDO {
	private $db;
	private $lastQuery;
	private $lastResult;

	public function __construct($host = null, $username = null, $password = null, $db = null) {
		if ($host && $username && $password && $db) {
			$this->db = new PDO("mysql:host=$host;dbname=$db", $username, $db);
		}
	}
	public function __destruct() {
		$this->db = null;
	}

	public function connect($host, $username, $password, $db) {
		$this->db = new PDO("mysql:host=$host;dbname=$db", $username, $password);
	}
	public function close() {
		$this->db = null;
	}

	public function execute($query) {
		$stmt = $this->db->query($query);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$this->lastQuery = $query;
		$this->lastResult = null;
		if (isset($result) && $result && !empty($result)) {
			$this->lastResult = $result;
			return $result;
		}
	}

	public function getLastQuery() { return $this->lastQuery; }
	public function getLastResult() { return $this->lastResult; }

	public function getTables() {
		return $this->execute("SHOW TABLES");
	}
	public function getFields($table) {
		if ($table && is_string($table) && !empty($table)) {
			return $this->execute("SHOW COLUMNS FROM $table");
		}
	}
}

?>