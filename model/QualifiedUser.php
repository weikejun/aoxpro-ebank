<?php

class My_Model_QualifiedUser {
	public static function insertUpdate($weiboId, $weiboName, $weiboPassport) {
		$res = My_Model_Base::getInstance()->query(
				'INSERT INTO `qualified_user` (`weibo_id`, `weibo_name`, `weibo_passport`, `date_time`) VALUES (:weibo_id, :weibo_name, :weibo_passport, :date_time) ON DUPLICATE KEY UPDATE `done_times` = `done_times` + 1',
				array(
					':weibo_id' => $weiboId, 
					':weibo_name' => $weiboName, 
					':weibo_passport' => $weiboPassport, 
					':date_time' => date("Ymd", time())
				     )
				);
		return !$res || $res->rowCount() 
			? true
			: false;
	}
}

