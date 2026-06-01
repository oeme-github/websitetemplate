<VirtualHost *:80>
    ServerName __DOMAIN__
    ServerAdmin webmaster@__DOMAIN__
    DocumentRoot __REPO_PATH__/public

    <Directory __REPO_PATH__/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog  ${APACHE_LOG_DIR}/__DOMAIN__-error.log
    CustomLog ${APACHE_LOG_DIR}/__DOMAIN__-access.log combined
</VirtualHost>
