<?php

include "includes/mysqli_db.php";

if (!isset($_SESSION)) {
	session_start();
}

class User {
	private $id;
	public $login;
	public $email;
	public $firstname;
	public $lastname;

	public function register($login, $password, $email, $firstname, $lastname) {
		global $db;

		$stmt = $db->prepare("SELECT * FROM users
		WHERE login = ?");
		$stmt->bind_param("s", $login);
		$stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();

		if (!$user) {
			$pwHash = password_hash($password, PASSWORD_BCRYPT);

			$stmt = $db->prepare("INSERT INTO users (login, password, email, firstname, lastname)
			VALUES (?, ?, ?, ?, ?)");
			$stmt->bind_param("sssss", $login, $pwHash, $email, $firstname, $lastname);
			$stmt->execute();

			return [
				"login" => $login,
				"password" => $pwHash,
				"email" => $email,
				"firstname" => $firstname,
				"lastname" => $lastname,
			];
		}
	}

	public function connect($login, $password) {
		if ($this->isConnected() && $password != false) return;

		global $db;

		$stmt = $db->prepare("SELECT * FROM users
		WHERE login = ?");
		$stmt->bind_param("s", $login);
		$stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();

		if ($user && ($password == false || password_verify($password, $user["password"]))) {
			foreach ($user as $key => $value) {
				if (property_exists($this, $key)) {
					$this->$key = $value;
				}
			}

			$_SESSION["user"] = $this;
			return $user;
		}
	}

	public function disconnect() {
		foreach ($this as $field => $value) {
			$this->$field = null;
		}

		unset($_SESSION["user"]);
	}

	public function isConnected() {
		if (isset($_SESSION["user"]) && isset($this->login) && $_SESSION["user"]->login == $this->login) {
			return true;
		}

		return false;
	}

	public function delete() {
		global $db;

		if ($this->isConnected()) {
			$stmt = $db->prepare("DELETE FROM users
			WHERE login = ?");
			$stmt->bind_param("s", $this->login);
			$stmt->execute();

			$this->disconnect();
		}
	}

	public function refresh() {
		if ($this->isConnected()) {
			$this->connect($this->login, false);
		}
	}

	public function update($login, $password, $email, $firstname, $lastname) {
		global $db;

		if ($this->isConnected()) {
			$pwHash = password_hash($password, PASSWORD_BCRYPT);

			$stmt = $db->prepare("UPDATE users
			SET login = ?, password = ?, email = ?, firstname = ?, lastname = ?
			WHERE login = ?");
			$stmt->bind_param("ssssss", $login, $pwHash, $email, $firstname, $lastname, $this->login);
			$stmt->execute();

			$this->refresh();
		}
	}

	public function getAllInfos() {
		$infos = [];

		foreach ($this as $key => $value) {
			$infos[$key] = $value;
		}

		return $infos;
	}

	public function getLogin() { return $this->login; }
	public function getEmail() { return $this->email; }
	public function getFirstname() { return $this->firstname; }
	public function getLastname() { return $this->lastname; }
}

?>