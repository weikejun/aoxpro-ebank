<VirtualHost *:80>
	ServerName hbr2013.cebnet.com.cn
	ServerAlias ebank.aoxpro.com
	ServerAdmin jim.dev@qq.com
	DocumentRoot /usr/local/apps/aoxpro_ebank/htdocs
	DirectoryIndex index.html index.php

	<Directory />
		Options FollowSymLinks
		AllowOverride None
	</Directory>

	<Directory /usr/local/apps/aoxpro_ebank/htdocs>
		Options Includes Indexes FollowSymLinks
		AllowOverride None
		Order allow,deny
		allow from all
		XBitHack on
	</Directory>
</VirtualHost>
