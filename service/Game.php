<?php

class My_Service_Game {
	public static function getScore($level, $rt, $addTime = 0) {
		$lvScore = array(
				0 => 160,
				1 => 240,
				2 => 300,
				3 => 480,
				4 => 560,
			      );
		$maxRt = array(
				0 => 76,
				1 => 74,
				2 => 72,
				3 => 67,
				4 => 65,
			      );
		if($addTime) {
			$maxRt[$level] += ConfigLoader::getInstance()->get('game', 'add_time');
		}
		$totalTime = ConfigLoader::getInstance()->get('game', 'total_time');
		$totalTimeScore = ConfigLoader::getInstance()->get('game', 'total_time_score');
		if(!isset($lvScore[$level])
				|| $rt > $maxRt[$level]) {
			return 0;
		}

		return $lvScore[$level] + intval($rt * 30);
	}

	public static function getTitle($score) {
		$titleList = array(
				0 => '金章连连小吉主',
				1 => '金章连连至尊',
				2 => '金章连连无敌圣',
				3 => '通天达地连连王',
				4 => '迪士尼金章连连摩天塔',
			      );
		$scoreList = array(1000, 2000, 3400, 5000);

		foreach($scoreList as $lv => $limit) {
			if($score < $limit) {
				return $titleList[$lv];
			}
		}

		return $titleList[4];
	}

	public static function getIP() 
	{ 
		if (isset($_SERVER)) { 
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
				$realip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) { 
				$realip = $_SERVER['HTTP_CLIENT_IP']; 
			} else { 
				$realip = $_SERVER['REMOTE_ADDR']; 
			} 
		} else { 
			if (getenv("HTTP_X_FORWARDED_FOR")) { 
				$realip = getenv( "HTTP_X_FORWARDED_FOR"); 
			} elseif (getenv("HTTP_CLIENT_IP")) { 
				$realip = getenv("HTTP_CLIENT_IP"); 
			} else { 
				$realip = getenv("REMOTE_ADDR"); 
			} 
		} 
		return $realip; 
	}
}
