<?php

abstract class My_Action_Abstract {
	protected $_actionName = 'index';

	protected $_actionKey = 'Action';

	protected $_viewParams = '';

	protected $_actionTime = '';

	public function __construct() {
		$this->_server = $_SERVER;
		$this->_actionTime = time();
	}

	public function getRequest($key = null) {
		if(is_null($key)) {
			return $_REQUEST;
		}
		return isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
	}

	public function setRequest($key, $value) {
		$_REQUEST[$key] = $value;
	}

	public function getSession($key = null) {
		if(is_null($key)) {
			return $_SESSION;
		}
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	public function setSession($session) {
		$_SESSION = $session;
		return $this;
	}

	public function getServer($key = null) {
		if(is_null($key)) {
			return $this->_server;
		}
		return isset($this->_server[$key]) ? $this->_server[$key] : null;
	}

	public function getActionTime() {
		return $this->_actionTime;
	}

	public function getActionName() {
		return $this->_actionName;
	}

	public function setViewParams($key, $value) {
		$this->_viewParams[$key] = $value;
	}

	public function renderView() {
		$viewScript = APP_ROOT . "/view/$this->_actionName.php";
		$content = '';
		if(file_exists($viewScript)) {
			ob_start();
			include $viewScript;
			$content = ob_get_contents();
			ob_end_clean();
		} else {}
		print $content;
	}

	public function redirect($url) {
		header("Location: $url");
		exit;
	}

	public function process() {
		$action = $this->getRequest('action') . $this->_actionKey;
		$this->_actionName = method_exists($this, $action) 
			? $this->getRequest('action')
			: $this->_actionName;	

		$this->_preAction();
		$this->{$this->_actionName . $this->_actionKey}();
		$this->_postAction();

		$this->renderView();
	}

	protected function _exit($msg = '') {
		die($msg);
	}

	abstract protected function _preAction();

	abstract protected function _postAction();
}
