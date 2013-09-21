<?php

class My_Service_Game {
	public static function verifyScore($score, $level) {
		$lvScore = array(
				0 => 20,
				1 => 30,
				2 => 40,
			      );
		if(!isset($lvScore[$level])
				|| $lvScore[$level] < $score) {
			return 0;
		}

		return $score;
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
