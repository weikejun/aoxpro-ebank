<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>授权页</title>
<link type="text/css" rel="stylesheet" href="css/style.css" />
</head>

<body>

	<div class="top_nav">
        <?php include WWW_ROOT . "/inc/logo_bar.dwt" ?>	
	</div>

	<div class="main1">
    	<div class="center bg_2">
	    <div title="HI，我是中国电子银行网吉祥物小V！我高大上么~" style="border: none; width: 143px; height: 150px; position: absolute; left: 942px; top: 69px; cursor:pointer;z-index:50;"></div>
        <?php include WWW_ROOT . "/inc/navi.dwt" ?>	
            <div class="clear"></div>
	</div>
    </div>
    
    <a name="login" />
    <div class="content">
    	<a name="play"></a>
    	<div class="sq">
        	<h5>请选择微博帐号进行授权</h5>
            <a href="/index.php?action=connect&platform=sina" title="新浪微博" class="a1"></a>
            <a href="/index.php?action=connect&platform=tencent" title="腾讯微博" class="a2"></a>
        </div>
    </div>
 
	<div class="footer"></div>
   
</body>
</html>
