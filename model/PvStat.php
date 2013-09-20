<?php

class My_Model_PvStat {
	private static $_PVID = 1;
	
	public static function incr() {
		$res = My_Model_Base::getInstance()->query(
				'INSERT INTO `pv_stat` (`id`, `count`) VALUES (:id, :count) ON DUPLICATE KEY UPDATE `count`=`count` + 1',
				array(
					':id' => self::$_PVID, 
					':count' => 1
				     )
				);
		return !$res || $res->rowCount() 
			? true
			: false;
	}

	public static function get() {
		$res = My_Model_Base::getInstance()->query(
				'SELECT * FROM `pv_stat` WHERE `id` = :id',
				array(
					':id' => self::$_PVID
				     )
				);
		return !$res || $res->rowCount() 
			? $res->fetchAll(PDO::FETCH_CLASS)
			: false;
	}
}

