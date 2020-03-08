#!/usr/bin/env bash

# only export does not seem to be enough for installing mysql
export DEBIAN_FRONTEND=noninteractive
sudo add-apt-repository ppa:ondrej/php
echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

sudo apt-get update && sudo apt-get upgrade

# Force a blank root password for mysql
echo "mysql-server mysql-server/root_password password " | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password " | debconf-set-selections

# Install mysql, nginx, php7.2-fpm
sudo apt-get install -q -y -f git mysql-server mysql-client nginx php7.2 php7.2-fpm php7.2-xdebug

# Install commonly used php packages
sudo apt-get install -q -y -f php7.2-curl php7.2-cli php7.2-mysqli php7.2-gd php7.2-dom php7.2-zip php7.2-mbstring

sudo apt-get upgrade libpcre3

sudo rm /etc/nginx/sites-available/default
sudo cp /var/www/etoa/vagrant/nginx-default /etc/nginx/sites-available/default
cp /var/www/etoa/vagrant/db.conf /var/www/etoa/htdocs/config
cp /var/www/etoa/vagrant/roundx.conf /vagrant/htdocs/config/eventhandler.conf

sudo service nginx restart
sudo service php7.2-fpm restart

MYSQL=`which mysql`
PHP=`which php`
Q0="SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));"
Q1="CREATE DATABASE IF NOT EXISTS etoa;"
Q2="GRANT USAGE ON *.* TO etoa@localhost IDENTIFIED BY 'etoa';"
Q3="GRANT ALL PRIVILEGES ON etoa.* TO etoa@localhost;"
Q4="CREATE DATABASE IF NOT EXISTS etoa_test;"
Q5="GRANT USAGE ON *.* TO etoa_test@localhost IDENTIFIED BY 'etoa';"
Q6="GRANT ALL PRIVILEGES ON etoa_test.* TO etoa@localhost;"
Q7="FLUSH PRIVILEGES;"
SQL="${Q0}${Q1}${Q2}${Q3}${Q4}${Q5}${Q6}${Q7}"
$MYSQL -uroot -e "$SQL"

$PHP /var/www/etoa/bin/db.php migrate
Q8="INSERT INTO config (config_name, config_value, config_param1, config_param2) VALUES ('loginurl','', '', '') ON DUPLICATE KEY UPDATE config_value='';"
$MYSQL -uroot -D etoa -e "$Q8"

# Setup cronjob
echo "* * * * * php /var/www/etoa/bin/cronjob.php" | crontab

# Install deps for eventhandler
sudo apt-get install -q -y -f cmake libboost-all-dev libmysql++-dev g++

# Build eventhandler
cd /var/www/etoa && php composer.phar install && make eventhandler
