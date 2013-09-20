<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<pre>
<?php
function echoSep() {
	echo str_pad('', 80, '-') . "\n";
}
function padStr($str) {
	return str_pad($str, 32, ' ');
}
echo "<h2>爆奖用户名单</h2>";
echoSep();
echo "|微博id     \t|" . padStr('微博用户名') . "\t|获奖时间\n";
echoSep();
foreach($this->_viewParams['data']['bonus'] as $user) {
	echo "|$user->weibo_id\t|" . padStr(trim($user->weibo_name)) . "\t|".date("Ymd H:i:s", $user->bonus_time) . "\n";
}
echoSep();
echo "<h2>用户分数排名</h2>";
echoSep();
echo "|微博id     \t|". padStr('微博用户名') . "\t|最高分\t|登录时间\n";
echoSep();
foreach($this->_viewParams['data']['rank'] as $user) {
	echo "|$user->weibo_id\t|" . padStr(trim($user->weibo_name)) . "\t|$user->high_score\t|".date("Ymd H:i:s", $user->login_time) . "\n";
}
echoSep();
?>
</pre>
</body>
</html>
