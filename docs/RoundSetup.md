# Setting up a new round

This example uses round15 which runs on 3.5-stable. Please make sure to adjust this to your needs.

##### Requirements (ask MrCage):
* Linux User-Account with sudo permission
* Knowledge of the MySQL root password

##### Setup directory
```sh
cd /var/lib/etoa
sudo -u etoa mkdir round15
cd round15
```

##### Checkout source code and select branch (use master for latest dev)
```sh
sudo -u etoa git clone git@github.com:etoa/etoa-gui .
sudo -u etoa git checkout 3.5-stable
```

##### Build eventhandler
```sh
sudo -u etoa eventhandler/bin/build.sh
```
##### SSL certificate
```sh
sudo letsencrypt certonly -a webroot --webroot-path=/var/www/html -d round15.game.etoa.net
```

##### Configure webserver
```sh
sudo cp /etc/nginx/sites-available/round14.trantor.etoa.net /etc/nginx/sites-available/round15.trantor.etoa.net
sudo sed 's/round14/round15/g' -i /etc/nginx/sites-available/round15.trantor.etoa.net
sudo ln -s /etc/nginx/sites-available/round15.trantor.etoa.net /etc/nginx/sites-enabled
sudo service nginx reload
```
If you still see the 404 page after this, check:
```sh
sudo service nginx check-reload
```

##### Generate db password
```sh
pwgen 20 1
```

##### Create database
```sh
mysql -u root -p
mysql> create database etoa_round15;
mysql> grant all privileges on etoa_round15.* to etoa_round15@localhost identified by '****************************';
mysql> exit;
```

##### Setup game
Open the game url in the browser: https://round15.trantor.etoa.net/
```
Server: localhost
Datenbank: etoa_round15
User: etoa_round15
Passwort: ****************************
```


Leave other settings as they are. Leave Login-Server URL empty as long as login from etoa.ch is not configured.

Setup eventhandler config:
``sh
sudo -u etoa cp htdocs/config/eventhandler.conf.dist htdocs/config/eventhandler.conf
sudo -u etoa nano htdocs/config/eventhandler.conf
``

Login as admin: https://round15.trantor.etoa.net/admin/

Adjust eventhandler config: (Rundenname):
Konfiguration -> Erweiterte Konfiguration -> Eventhandler

Create universe
Create admin user

Setup cronjob for periodic task (check on page "Periodische Tasks")
``sh
sudo -u etoa crontab -e
``

Add:
````sh
* * * * * /var/lib/etoa/round15/bin/cronjob.php
```
