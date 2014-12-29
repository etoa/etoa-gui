#!/usr/bin/env bash

# only export does not seem to be enough for installing mysql
export DEBIAN_FRONTEND=noninteractive
echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

sudo aptitude update -q

sudo aptitude install -g -y -f htop git

# Force a blank root password for mysql
echo "mysql-server mysql-server/root_password password " | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password " | debconf-set-selections

# Install mysql, nginx, php5-fpm
sudo aptitude install -q -y -f mysql-server mysql-client nginx php5-fpm

# Install commonly used php packages
sudo aptitude install -q -y -f php5-curl php5-mcrypt php5-cli php5-mysql php5-gd

sudo rm /etc/nginx/sites-available/default
sudo cp /var/www/etoa/vagrant/nginx-default /etc/nginx/sites-available/default
cp /var/www/etoa/vagrant/db.conf /var/www/etoa/htdocs/config

sudo service nginx restart
sudo service php5-fpm restart

MYSQL=`which mysql`
PHP=`which php`
  
Q1="CREATE DATABASE IF NOT EXISTS etoa;"
Q2="GRANT USAGE ON *.* TO etoa@localhost IDENTIFIED BY 'etoa';"
Q3="GRANT ALL PRIVILEGES ON etoa.* TO etoa@localhost;"
Q4="FLUSH PRIVILEGES;"
SQL="${Q1}${Q2}${Q3}${Q4}"
$MYSQL -uroot -e "$SQL"

$PHP /var/www/etoa/bin/db.php migrate
Q5="INSERT INTO config (config_name, config_value) VALUES ('loginurl','') ON DUPLICATE KEY UPDATE config_value='';"
$MYSQL -uroot -D etoa -e "$Q5"

# Install deps for eventhandler
sudo aptitude install -q -y -f cmake libboost-all-dev libmysql++-dev g++

# Build eventhandler
cd /var/www/etoa/eventhandler
cmake .
make

sudo mkdir -p /etc/etoad
sudo mkdir -p /var/log/etoad
sudo mkdir -p /var/run/etoad
sudo chmod -R 777 /var/log/etoad
sudo chmod -R 777 /var/run/etoad
sudo cp /var/www/etoa/vagrant/roundx.conf /etc/etoad/roundx.conf
sudo su vagrant -c"./target/etoad roundx -k -d"
