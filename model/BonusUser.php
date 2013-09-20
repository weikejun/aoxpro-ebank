<?php

class My_Model_BonusUser {
	public static function create($weiboId, $timestamp) {
		$res = My_Model_Base::getInstance()->query(
				'INSERT INTO `bonus_user` (`weibo_id`, `bonus_time`) VALUES (:weibo_id, :bonus_time) ',
				array(
					':weibo_id' => $weiboId, 
					':bonus_time' => $timestamp
				     )
				);
		return !$res || $res->rowCount()
			? true
			: false;
	}

	public static function hasBonus($weiboId) {
		$has = true;
		$res = My_Model_Base::getInstance()->query(
				'SELECT COUNT(*) as total FROM `bonus_user` WHERE `weibo_id` = :weibo_id ',
				array(':weibo_id' => $weiboId)
				);
		if(!$res || $res->rowCount()) {
			$obj = $res->fetchAll(PDO::FETCH_CLASS);
			$has = $obj[0]->total;
		}
		return $has;
	}

	public static function getBonusList() {
		$res = My_Model_Base::getInstance()->query(
				'SELECT u.`weibo_id`, u.`weibo_name`, bu.`bonus_time` FROM `bonus_user` bu LEFT JOIN `user` u ON (bu.`weibo_id` = u.`weibo_id`)',
				array()
				);
		return empty($res) ? array() : $res->fetchAll(PDO::FETCH_CLASS);
	}
}

