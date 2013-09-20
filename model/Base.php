<?php

class My_Model_Base {
	protected static $_instance = null;
	protected $_pdo = null;
	
	private function __construct() {
		$config = ConfigLoader::getInstance()->get('db');
		$this->_pdo = new PDO(
				$config['dsn'],
				$config['user'],
				$config['pass']
				);
		if(empty($this->_pdo)) {
			return null;
		}
		return $this;
	}

	public function query($sql, array $params) {
		$stmt = $this->_pdo->prepare($sql);
		return $stmt->execute($params)
			? $stmt
			: false;
	}

	public function getConnection() {
		return $this->_pdo;
	}

	public static function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
