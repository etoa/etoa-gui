Escape to Andromeda Installation Guide
======================================

Requirements
------------

* PHP 5.6 with the following extensions
** mysql
** gd
** curl
* composer
* MySQL/MariaDB
* Linux-based OS for the eventhandler

Frontend - Version 1.0

Vagrant Developer setup
-----------------------

Install [VirtualBox](https://www.virtualbox.org/).
Install [Vagrant](https://www.vagrantup.com/).

Install [Network File System](https://en.wikipedia.org/wiki/Network_File_System) if necessary. Start/Enable NFS-Server with UDP and Version 3 support.

In your etoa root run ```make install```
* PHP 7.3 is not supported (7.2 works fine)
* On Linux Vagrant needs sudo privileges. Instead of ```make install``` use

  ```$ php72 composer.phar install && sudo vagrant up --provision```

You can now reach etoa via http://192.168.33.11
Cronjob, Eventhandler, DB, PHP, Nginx should be running.

Additional steps because I haven't figured out yet how to reset the db config via cli:
* Go to: http://192.168.33.11/admin/
* Login as admin
* Reset the Configuration in the admin tool, then set the loginurl back to "" (empty string)

#### Troubleshooting
* Eventhandler offline: Check path to pid, e.g. ```/run/etoad/roundx.pid```
* Guest Additions: ```# vagrant plugin install vagrant-vbguest```

All steps below are only necessary if you dont want to use the vagrant box!!!!

Files
-----
Copy all files to the gameservers root directory, e.g. /var/www/roundx.etoa.net/htdocs,
or user svn checkout:

    $ svn co http://dev.etoa.net/svn/game/trunk /var/www/roundx.etoa.net/htdocs


Permissions
-----------
Execute the following command:

    $ chmod 777 -R /var/www/roundx.etoa.net/htdocs/cache

to allow writing to the cache directory


Config
------
Edit the example.conf.inc.php file to your needs and rename it to conf.inc.php

PASSWORD_SALT is a random choosen value, which has to be set at the begining of a round and should not be changed during a round


Scripts
-------
Execute scripts/reset_admin_pw.sh for setting the admin mode htaccess password to it's default value
(default user and passwort will be shown at the end of the above script)


Cronjobs
--------
On the unix shell, execute

    $ crontab -e

this will open the cron editor. Insert the following text (when using vi as editor, press INSERT first)

    * * * * * php /path/to/etoa/bin/cronjob.php

Save and exit (in vi, press CTRL+C, then write wq and press ENTER), type

    $ crontab -l

to verify your settings.


Misc
----

The admin panel can be accessed at roundx/admin.

 * Go to Admin-Panel => Config => Imagepacks and generate the downloadable imagepack files
 * Go to Admin-Panel => Config => Generate Universe to create the universe for this round

Debug mode
----------

Create an empty file `config/debug` to enable the debug mode.


Sample installation on host.etoa.net
------------------------------------

Assuming round name 'round12'.

Create directory and checkout code from SCM:

    $ mkdir -p /var/www/etoa/round12/htdocs
    $ ln -s /var/www/etoa/round12/htdocs ~/round12
    $ cd ~/round12
    $ svn co https://devel.etoa.net/svn/etoa-gui/trunk/htdocs

Set permissions on cache directory:

    $ chmod -R 777 cache/

Create database config:

    $ cp config/db.conf.dist config/db.conf
    $ vim config/db.conf

Create database and user and import schema and data sql using e.g. phpMyAdmin on http://host.etoa.net/phpmyadmin

Create apache config (Root account required):

    $ vim /etc/apache2/sites-available/4_etoa_round12

    <VirtualHost *:80>
            ServerAdmin mail@etoa.ch
            DocumentRoot "/var/www/etoa/round12/htdocs/"
            ServerName round12.live.etoa.net
            DirectoryIndex index.php index.html
            ErrorLog /var/log/apache2/round12.live.etoa.net_error_log
            CustomLog /var/log/apache2/round12.live.etoa.net_access_log combined
            <Directory "/var/www/etoa/round12/htdocs">
                    Options -Indexes
                    AllowOverride All
                    Order allow,deny
                    Allow from all
            </Directory>
            ErrorDocument 401 /error/error.php
            ErrorDocument 403 /error/error.php
            ErrorDocument 404 /error/error.php
    </VirtualHost>

    $ ln -s /etc/apache2/sites-available/4_etoa_round12 /etc/apache2/sites-enabled/
    $ service apache2 reload

Access admin panel on http://round12.live.etoa.net/admin

Visit the base config page on [http://round12.live.etoa.net/admin/?page=config](http://round12.live.etoa.net/admin/?page=config) and change the settings to match the round name.

The eventhandler IPC-Key can be obtained by starting the eventhandler backend for this round in debug mode.


Setup test instance on an Ubuntu server
---------------------------------------

Install Ununtu server, choose OpenSSH and LAMP as server profile.

GUI:

    sudo apt-get install subversion git phpmyadmin
    sudo mkdir -p /var/www/etoa/test
    sudo chown -R etoa /var/www/etoa/test
    ln /var/www/etoa/test ~/test -s
    svn co https://dev.etoa.net/svn/etoa-gui/trunk/htdocs ~/test

Create database and database user using phpmyadmin

Backend:

    supt apt-get install build-essential cmake libboost-all-dev libmysql++-dev
    svn co https://dev.etoa.net/svn/etoa-eventhandler/trunk ~/backend
    cd ~/backend
    cmake .
    make
    sudo mkdir /var/run/etoad/
    sudo chown etoa /var/run/etoad/
    sudo mkdir /var/log/etoad
    bin/etoad test --debug -c ~/test/config/
