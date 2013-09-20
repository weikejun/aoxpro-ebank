<?php

class My_Model_BonusQuota {
	public static function getQuota() {
		$res = My_Model_Base::getInstance()->query(
				'UPDATE `bonus_quota` SET `quota` = `quota` - 1 WHERE `quota` > 0 AND `id` = 1',
				array()
				);
		return !$res || $res->rowCount()
			? true
			: false;
	}

	public static function updateQuota() {
		$res = My_Model_Base::getInstance()->query(
				'INSERT INTO `bonus_quota` (`id`, `quota`) VALUES(1, :quota) ON DUPLICATE KEY UPDATE `quota` = :quota',
				array(':quota' => ConfigLoader::getInstance()->get('game', 'bonus_quota'))
				);
		return !$res || $res->rowCount()
			? true
			: false;
	}
}

