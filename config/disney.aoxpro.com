<VirtualHost *:80>
	ServerName disney.aoxpro.com
	ServerAdmin jim.dev@qq.com
	DocumentRoot /var/www/htdocs

	<Directory />
		Options FollowSymLinks
		AllowOverride None
	</Directory>

	<Directory /var/www/htdocs>
		Options Indexes FollowSymLinks MultiViews
		AllowOverride None
		Order allow,deny
		allow from all
	</Directory>
</VirtualHost>
