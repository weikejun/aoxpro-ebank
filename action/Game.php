<?php

class My_Action_Game extends My_Action_Abstract {
	private $_weiboService = null;

	private $_exception = null;

	private $_weiboUser = null;

	private $_platformKey = array('sina', 'tencent');

	protected $_isAuth = false;

	public function loginAction() {
		$ret = null;
		try {
			$sParams = $this->getSession('oauth2');
			$user = $this->_weiboUser;
			if(empty($user) || !empty($user['error'])) {
				throw new Exception('get user error');
			}
			$ret = My_Model_User::insertUpdate(
					$user['id'], 
					$user['name'],
					$this->getActionTime()
					);
			My_Model_UserStatus::deleteByWeiboId($sParams['user_id']);
		} catch (Exception $e) {
			$this->_exception = $e;
		}

		$this->setViewParams(
				'data', 
				array('success' => !empty($ret) ? 1 : 0)
				);
	}

	public function infoAction() {
		$user = null;
		try {
			$sParams = $this->getSession('oauth2');
			$user = My_Model_User::getByWeiboId($sParams['user_id']);
			if(empty($user)) {
				throw new Exception('get user error');
			}
		} catch (Exception $e) {
			$this->_exception = $e;
		}

		$this->setViewParams('data', array(
					'success' => !empty($user) ? 1 : 0,
					'weibo_name' => !empty($user) ? $user[0]->weibo_name : '',
					'high_score' => !empty($user) ? $user[0]->high_score : '',
					'title' => !empty($user) ? My_Service_Game::getTitle($user[0]->high_score) : '',
					));
	}

	public function playAction() {
		$sParams = $this->getSession('oauth2');
		$conn = My_Model_Base::getInstance()->getConnection();
		try {
			$conn->beginTransaction();
			$user = My_Model_User::getByWeiboId($sParams['user_id']);
			if(empty($user)) {
				throw new Exception('get user error');
			}
			$ret = My_Model_UserStatus::startPlay(
					$sParams['user_id'], 
					$this->getActionTime()
					);
			if(empty($ret)) {
				throw new Exception('update user status error');
			}
			$status = My_Model_UserStatus::getByWeiboId($sParams['user_id']);

			if(!My_Model_BonusUser::hasBonus($sParams['user_id'])) {
				($bonus = $this->_getBonus()) && My_Model_BonusUser::create(
						$sParams['user_id'],
						$this->getActionTime()
						);
			}

			$conn->commit();
		} catch (Exception $e) {
			$this->_exception = $e;
			$conn->rollBack();
		}

		$this->setViewParams('data', array(
					'success' => !empty($status) ? 1 : 0,
					'level' => !empty($status) ? $status[0]->level : 0,
					'total_score' => !empty($status) ? $status[0]->total_score : 0,
					'bonus' => !empty($bonus) ? 1 : 0,
					));
	}

	public function passAction() {
		$status = null;
		$rank = 0;
		$sParams = $this->getSession('oauth2');
		$rt = $this->getRequest('rt');
		$score = 0;

		$conn = My_Model_Base::getInstance()->getConnection();
		try {
			$conn->beginTransaction();
			if(is_null($rt) || $rt === '') {
				$rt = 0;
				throw new Exception('remaining time invalid');
			} else {
				$rt = ceil($rt);
			}
			$status = My_Model_UserStatus::getByWeiboId($sParams['user_id']);
			if(empty($status) || empty($status[0])) {
				throw new Exception('get user status error');
			}
			if($status[0]->status != My_Model_UserStatus::STATUS_PLAY) {
				throw new Exception('user not play');
			}
			if($this->getActionTime() - $status[0]->level_time + $rt - intval($this->getRequest('addTime'))*ConfigLoader::getInstance()->get('game', 'add_time')  < ConfigLoader::getInstance()->get('game', 'total_time')) {
				throw new Exception('rt error');
			}
			$score = My_Service_Game::getScore($status[0]->level, $rt, $this->getRequest('addTime'));
			if($score == 0) {
				throw new Exception('count score error');
			}
			$status[0]->total_score += $score;
			$status[0]->level += 1;
			if($status[0]->level > ConfigLoader::getInstance()->get('game', 'max_level')) {
				$status[0]->level = My_Model_UserStatus::LEVEL_FINISH;
			}
			$status[0]->level_time = $this->getActionTime();
			$status[0]->status = My_Model_UserStatus::STATUS_IDLE;
			if(!My_Model_UserStatus::updateUserStatus($status[0])) {
				throw new Exception('update user status error');
			}
			$rank = My_Model_User::getScoreRank($status[0]->total_score);
			if(!My_Model_User::updateUserHighScore($sParams['user_id'], $status[0]->total_score)) {
				throw new Exception('update user high score error');
			}
			$conn->commit();
		} catch (Exception $e) {
			$this->_exception = $e;
			$status = null;
			$conn->rollBack();
		}

		$this->setViewParams('data', array(
					'success' => !empty($status) ? 1 : 0,
					'level_score' => !empty($status) ? $score : 0,
					'total_score' => !empty($status) ? $status[0]->total_score : 0,
					'rank' => !empty($status) ? $rank : 0,
					'level_next' => !empty($status) ? $status[0]->level : 0
					));
	}

	public function shareAction() {
		$sParams = $this->getSession('oauth2');
		$avatarId = $this->getRequest('avatar_id');
		if(empty($avatarId)) {
			$avatarId = rand(1,9);
		}
		$content = $this->getRequest('content');
		if(!is_null($content)) {
			$content = urldecode($content);	
		} else {
			$status = My_Model_UserStatus::getByWeiboId($sParams['user_id']);
			if(empty($status)) {
				$content = ConfigLoader::getInstance()->get('share', 'content_error');
			} else {
				$content = sprintf(
						ConfigLoader::getInstance()->get('share', 'content'),
						My_Service_Game::getTitle($status[0]->total_score),
						(100 - My_Model_User::getScoreRank($status[0]->total_score)) . '%'
						);
			}
			$content . ConfigLoader::getInstance()->get('share', 'join_tips');
		}
		$this->_weiboService->upload(
				$content,
				sprintf(ConfigLoader::getInstance()->get('share', 'pic_url'), $avatarId)
				);
		$this->setViewParams('data', array('success' => 1));
	}

	public function getShareAction() {
		$sParams = $this->getSession('oauth2');
		$status = My_Model_UserStatus::getByWeiboId($sParams['user_id']);
		if(empty($status)) {
			$content = ConfigLoader::getInstance()->get('share', 'content_error');
		} else {
			$content = sprintf(
					ConfigLoader::getInstance()->get('share', 'content'),
					My_Service_Game::getTitle($status[0]->total_score),
					(100 - My_Model_User::getScoreRank($status[0]->total_score)) . '%25'
					);
		}
		$this->setViewParams('data', 
				array(
					'success' => 1,
					'content' => $content . ConfigLoader::getInstance()->get('share', 'join_tips'),
				     )
				);
	}

	public function indexAction() {
	}

	public function callbackAction() {
		$defUrl = "http://" . $this->getServer('HTTP_HOST');
		$session = $this->getSession();
		$platform = $session['platform'];
		$ru = $session['ru'];
		if (empty($ru)) {
			$ru = $defUrl;
		}

		if ($platform == 'sina') { // sina authorize
			$o = new SaeTOAuthV2(WB_AKEY , WB_SKEY);
			$code = $this->getRequest('code');
			$token = null;
			if (!empty($o) && !empty($code)) {
				try {
					$token = $o->getAccessToken('code', array(
								'code' => $code,
								'redirect_uri' => $ru
								)) ;
				} catch (OAuthException $e) {}
			}


			if ($token) { // 授权成功
				$session[$platform]['token'] = $token;
				$this->setSession($session);
				setcookie('weibojs_' . $o->client_id, http_build_query($token));
				$this->redirect($ru);
			} 
		} else { // tencent authorize
			OAuth::init(TX_AKEY, TX_SKEY);
			Tencent::$debug = TX_DEBUG;
			$code = $this->getRequest('code');
			$openid = $this->getRequest('openid');
			$openKey = $this->getRequest('openKey');
			//获取授权token
			$host = $this->getServer('HTTP_HOST');
			$url = OAuth::getAccessToken($code, "http://$host/index.php?action=callback");
			$r = Http::request($url);
			parse_str($r, $out);
			//存储授权数据
			if ($out['access_token']) {
				$session[$platform]['t_access_token'] = $out['access_token'];
				$session[$platform]['t_refresh_token'] = $out['refresh_token'];
				$session[$platform]['t_expire_in'] = $out['expires_in'];
				$session[$platform]['t_code'] = $code;
				$session[$platform]['t_openid'] = $openid;
				$session[$platform]['t_openkey'] = $openkey;
				$this->setSession($session);

				//验证授权
				$r = OAuth::checkOAuthValid();
				$this->redirect($ru);
			} 
		}

		$this->redirect($defUrl);
	}

	public function gameAction() {
		echo 'flash game';
		exit;
	}

	public function pvAction() {
		$ret = null;
		$pv = 0;
		try {
			My_Model_PvStat::incr();
			$ret = My_Model_PvStat::get();
			$pv = $ret ? $ret[0]->count : $pv;
		} catch (Exception $e) {
			$this->_exception = $e;
		}

		$this->setViewParams(
				'data', 
				array('pv' => $pv)
				);
	}

	public function authAction() {
	}

	public function connectAction() {
		// set platform
		$p = $this->getRequest('platform');
		if (!in_array($p, $this->_platformKey)) {
			$p = $this->_platformKey[0];
		}
		$session = $this->getSession();
		$session['platform'] = $p;
		$this->setSession($session);

		// set callback url
		$host = $this->getServer('HTTP_HOST');
		$ru = $session['ru'];
		$callbackUrl = "http://$host/index.php?action=callback";

		// authorize
		if ($p == 'sina') { // sina authorize
			$o = new SaeTOAuthV2(WB_AKEY, WB_SKEY);
			$url = $o->getAuthorizeURL($callbackUrl);
		} else { // tencent authorize
			OAuth::init(TX_AKEY, TX_SKEY);
			Tencent::$debug = TX_DEBUG;
			$url = OAuth::getAuthorizeURL($callbackUrl);
		}

		// redirect
		$this->redirect($url);
	}

	public function unauthAction() {}

	public function statAction() {
		$this->setViewParams('data', 
				array(
					'bonus' => My_Model_BonusUser::getBonusList(),
					'rank' => My_Model_User::getOrderList(),
				     )
				);
	}

	protected function _postAction() {
//		$sParams = $this->getSession('oauth2');
//		$actionBody = sprintf(
//				'ip=%s|sid=%s|msg=%s|uri=%s',
//				My_Service_Game::getIP(),
//				session_id(),
//				is_null($this->_exception) ? 'done' : $this->_exception->getMessage(),
//				$this->getServer('REQUEST_URI')
//				);
//		My_Model_ActionLog::logAction(
//				empty($sParams['user_id']) ? 0 : $sParams['user_id'],
//				$this->getActionName(),
//				$actionBody,
//				$this->getActionTime()
//				);
	}

	protected function _preAction() {
		if('callback' != $this->getActionName()
				&& 'connect' != $this->getActionName()
				&& 'auth' != $this->getActionName()
				&& 'pv' != $this->getActionName()) {
			$this->_verifyAuth();
		}
	}

	private function _verifyAuth() {
		$session = $this->getSession();
		$host = $this->getServer('HTTP_HOST');
		$ru = "http://$host" . $this->getServer('REQUEST_URI');
		if (!isset($session['platform'])
				|| !isset($session[$session['platform']])) {
			$session['ru'] = $ru;
			$this->setSession($session);
			$this->redirect("http://$host/index.php?action=auth");
		}

		return $this->_isAuth = true;

#		$this->_weiboService = new SaeTClientV2( 
#				WB_AKEY, 
#				WB_SKEY,
#				$sessOauth['oauth_token'],
#				''
#				);
#		$this->_weiboUser = !empty($this->_weiboService) 
#			? $this->_weiboService->show_user_by_id($sessOauth['user_id'])
#			: null;
#		if(empty($this->_weiboUser) || !empty($this->_weiboUser['error'])) {
#			if($this->_actionName != 'index') {
#				$this->_actionName = 'auth';
#			}
#			return false;
#		}
#
#		return $this->_isAuth = true;
	}

	private function _getBonus() {
		$bonus = false;
		$gameCfg = ConfigLoader::getInstance()->get('game');
		$r = rand(1, $gameCfg['bonus_rate']);
		if($r <= $gameCfg['bonus_quota']) {
			$bonus = My_Model_BonusQuota::getQuota();
		}
		return $bonus;
	}

	private function _getFollowerRandom($userId) {
		$followers = $this->_weiboService->followers_by_id($userId, 0, 200);
		if(!empty($followers) 
				&& !empty($followers['users'])) {
			shuffle($followers['users']);
		}
		$flrAr = array();
		for($i = 0; $i < 3; $i++) {
			if(isset($followers['users'][$i])) {
				$follower = $followers['users'][$i];
				if(empty($follower['name'])) {
					$follower['name'] = $follower['id'];
				}
				$flrAr[] = '@'.$follower['name'];
			}
		}
		return $flrAr;
	}

}
