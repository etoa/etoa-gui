#!/usr/bin/env bash

# only export does not seem to be enough for installing mysql
export DEBIAN_FRONTEND=noninteractive
sudo add-apt-repository ppa:ondrej/php
echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

sudo apt-get update -q && sudo apt-get upgrade -q

# Force a blank root password for mysql
echo "mysql-server mysql-server/root_password password " | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password " | debconf-set-selections

# Install mysql, nginx, php8.1-fpm
sudo apt-get install -q -y -f --no-install-recommends git mysql-server mysql-client nginx php8.1 php8.1-fpm php8.1-xdebug

# Install commonly used php packages
sudo apt-get install -q -y -f php8.1-curl php8.1-cli php8.1-mysql php8.1-gd php8.1-zip php8.1-mbstring php8.1-intl php8.1-redis php8.1-xml

sudo apt-get upgrade -q libpcre3

# install nodejs and yarn npm pkg
sudo apt-get -y -q install curl dirmngr apt-transport-https lsb-release ca-certificates unzip
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y -q nodejs
npm install --global yarn

# Setup dummy client files
cd /var/www/etoa && mkdir -p htdocs/web/build && echo "{}" > htdocs/web/build/manifest.json && echo '{"entrypoints": {"admin": {}}}' > htdocs/web/build/entrypoints.json

# Install PHP composer dependencies
cd /var/www/etoa && export COMPOSER_ALLOW_SUPERUSER=1;php composer.phar install --no-interaction

# install yarn dependencies
cd /var/www/etoa && yarn install --frozen-lockfile

sudo rm /etc/nginx/sites-available/default
sudo cp /var/www/etoa/vagrant/nginx-default /etc/nginx/sites-available/default
sudo cp /var/www/etoa/vagrant/xdebug.ini /etc/php/8.0/mods-available/xdebug.ini
cp /var/www/etoa/vagrant/db.conf /var/www/etoa/htdocs/config
cp /var/www/etoa/vagrant/roundx.conf /var/www/etoa/htdocs/config/eventhandler.conf

sudo service nginx restart
sudo service php8.0-fpm restart
sudo chown -R www-data:www-data /var/lib/php/sessions

MYSQL=`which mysql`
PHP=`which php`

# trigger yarn build
cd /var/www/etoa && yarn run build

# Setup database
Q0="SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));"
Q1="CREATE DATABASE IF NOT EXISTS etoa;"
Q2="CREATE USER 'etoa'@'localhost' IDENTIFIED BY 'etoa';"
Q3="GRANT ALL PRIVILEGES ON etoa.* TO etoa@localhost;"
Q4="CREATE DATABASE IF NOT EXISTS etoa_test;"
Q6="GRANT ALL PRIVILEGES ON etoa_test.* TO etoa@localhost;"
Q7="FLUSH PRIVILEGES;"
SQL="${Q0}${Q1}${Q2}${Q3}${Q4}${Q6}${Q7}"
$MYSQL -uroot -e "$SQL"

$PHP /var/www/etoa/bin/console database:migrate
$PHP /var/www/etoa/bin/console database:migrate --env=test
Q8="INSERT INTO config (config_name, config_value, config_param1, config_param2) VALUES ('loginurl','', '', '') ON DUPLICATE KEY UPDATE config_value='';"
$MYSQL -uroot -D etoa -e "$Q8"

# Allow world write access to tmp directory
sudo chmod 777 /var/www/etoa/htdocs/tmp

# Setup cronjob
echo "* * * * * php /var/www/etoa/bin/console cron:run" | crontab

# Install deps for eventhandler
sudo apt-get install -q -y -f cmake libboost-all-dev libmysql++-dev g++

# Build eventhandler
cd /var/www/etoa && make eventhandler
