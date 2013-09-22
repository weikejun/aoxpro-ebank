<?php
defined('WWW_ROOT') ? '' : define('WWW_ROOT', dirname(__FILE__) . '/htdocs');
defined('APP_ROOT') ? '' : define('APP_ROOT', dirname(__FILE__));
define('WB_AKEY', '2290085142');
define('WB_SKEY', '315a4f451b5955d816bc34b7f1f0822d');
define('CANVAS_PAGE' , 'http://disney.aoxpro.com/index.php');
define('CLASS_PREFIX', 'My_');
define('TX_AKEY', '801417614');
define('TX_SKEY', '6a0e17de64ddd6511ee7b80f95c91d9e');
define('TX_DEBUG', false);

session_name('usid');
session_start();

function  __autoload($class) {
	if(strncasecmp($class, CLASS_PREFIX, strlen(CLASS_PREFIX)) === 0) {
		list($prefix, $dir, $classname) = explode('_', $class);
		$dir = strtolower($dir);
		return require_once(APP_ROOT . "/$dir/$classname.php");
	}

	require_once(APP_ROOT . '/lib/saetv2.ex.class.php');
	require_once(APP_ROOT . '/lib/Tencent.php');
}

class ConfigLoader {
	private static $_instance = null;
	private $_gConfig = array();

	private function __construct() {
		include_once(APP_ROOT . '/config/config.php');
		$this->_gConfig = $gConfig;
	}

	public static function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function get($class = null, $item = null) {
		if(is_null($class)) {
			return $this->_gConfig;
		}
		if(is_null($item)) {
			return $this->_gConfig[$class];
		}
		return $this->_gConfig[$class][$item];
	}
}

ConfigLoader::getInstance();
