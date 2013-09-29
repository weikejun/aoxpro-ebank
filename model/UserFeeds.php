<?php

class My_Model_UserFeeds {
	public static function insert($weiboId, $weiboName, $weiboFace, $feedsDetail, $feedsPic, $platform) {
		$res = My_Model_Base::getInstance()->query(
				'INSERT INTO `user_feeds` (`weibo_id`, `weibo_name`, `weibo_face`, `feeds_pic`, `feeds_detail`, `platform`, `create_time`) VALUES (:weibo_id, :weibo_name, :weibo_face, :feeds_pic, :feeds_detail, :platform, :create_time)',
				array(
					':weibo_id' => $weiboId, 
					':weibo_name' => $weiboName, 
					':weibo_face' => $weiboFace, 
					':feeds_pic' => $feedsPic, 
					':feeds_detail' => $feedsDetail, 
					':platform' => $platform, 
					':create_time' => time()
				     )
				);
		return $res && $res->rowCount() 
			? true
			: false;
	}

	public static function get($platform, $offset, $limit) {
		$res = My_Model_Base::getInstance()->query(
				'SELECT weibo_id, weibo_name, weibo_face, feeds_pic, feeds_detail, from_unixtime(create_time, "%m-%d %H:%i") as create_time FROM `user_feeds` WHERE `platform` = :platform ORDER BY `create_time` DESC LIMIT ' . "$offset,$limit",
				array(
					':platform' => $platform,
				     )
				);
		return $res && $res->rowCount() 
			? $res->fetchAll(PDO::FETCH_ASSOC)
			: false;
	}

	public static function total($platform) {
		$res = My_Model_Base::getInstance()->query(
				'SELECT count(*) as `total` FROM `user_feeds` WHERE `platform` = :platform',
				array(':platform' => $platform)
				);
		return $res && $res->rowCount() 
			? $res->fetchAll(PDO::FETCH_ASSOC)
			: false;
	}
}

