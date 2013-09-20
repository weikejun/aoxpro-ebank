<?php

class My_Model_ActionLog {
	public static function logAction($weiboId, $actionName, $actionBody, $timestamp) {
		$res = My_Model_Base::getInstance()->query(
				'INSERT INTO `action_log` (`weibo_id`, `action_name`, `action_body`, `action_time`) VALUES (:weibo_id, :action_name, :action_body, :action_time)',
				array(
					':weibo_id' => $weiboId, 
					':action_name' => $actionName, 
					':action_body' => $actionBody, 
					':action_time' => $timestamp
				     )
				);
		return $res;
	}
}

