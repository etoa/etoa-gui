EtoA Eventhandler (etoad)
=========================

A backend for handling events in the Escape to Andromeda game written in C++.

Escape to Andromeda (EtoA) is a MMO Sci-Fi Browsergame: http://etoa.ch

Building
--------

### Requirements ###

 * Linux C++ build chain
 * CMake
 * Boost library (filesystem, regex, system, thread)
 * MySQL++ library

If you use Debian Linux, you can simply execute

	apt-get install build-essential cmake libboost-all-dev libmysql++-dev

to install all dependencies.

### Compiling ###

Execute

	bin/build.sh

to create all Makefiles using Cmake and compile the code.

The compiled binary will be available in the `target/` directory.

Use the command 

	bin/make-deb.sh
	
to create a Debaian Linux package containing the binary, a sample configuration and an init start script.

### Releasing ###

Define release version and next development version:

    VERSION=3.0.2
    DEV_VERSION=3.X.X-SNAPSHOT
    
Create SCM tag:

    bin/set-version.sh $VERSION
    git add src/version.h
    git commit -m "[Release $VERSION]"
    git tag $VERSION
    
Build source and create debian package:

    bin/build.sh
    bin/make-deb.sh
    
Prepare for further development:

    bin/set-version.sh $DEV_VERSION
    git add src/version.h
    git commit -m "[Prepare for next development iteration $DEV_VERSION]"

Push changes:

    git push
    git push --tags
    
Usage
-----

### Standalone ###

Each instance of an EtoA game has its data stored in a dedicated MySQL database. 
Databse access is defined in a config file residing in `/etc/etoad/` (Path can be changed with command line parameter).

The config file has the following layout:

	[mysql]
	host = localhost
	database = etoa_roundX
	user = etoa_roundX
	password = YOUR_PASSWORD

The etoa eventhandler can be started in debug mode by executing

	etoad <INSTANCE> --debug

where `<INSTANCE>` determines the path of the config file (`/etc/etoad/<INSTANCE>.conf`) and the path of 
the PID file used to keep track of the process id (`/var/run/etoad/<INSTANCE>.pid`). 
Ensure that the user has write permissions to the PID directory. Running `etoad` as root is discouraged
for security reasons and will result in an error message being thrown.
Use CTRL+C to stop the process.

The etoa eventhandler can be started as background process by executing

	etoad <INSTANCE> -d

and stopped by executing

	etoad <INSTANCE> -s

Further command line parameters:
	
	Options:
  	-d, --daemon            Detach from console and run as daemon in background
  	-s, --stop              Stops a running instance of this backend
	
  	-p, --pidfile path      Path to PID file (default: /var/run/etoad/INSTANCE.pid)
  	-c, --config path       Path to config file (default: /etc/etoad/INSTANCE.conf)
  	-u, --uid userid        Select user id under which it runs (necessary if you are root)
  	-k, --killexisting      Kills an already running instance of this backend before starting this instance
  	-l, --log level         Specify log level (0=emerg, ... , 7=everything

	    --debug             Enable debug mode
  	-h, --help              Prints this help
      	--version           Prints version information

### Debian Setup ###

Executing `bin/make-deb.sh` will create a Debian package file like `etoa-eventhandler_<VERSION>_<ARCHITECTURE>.deb` in the `target/` directory which can be installed by executing:

	dbkg -i etoa-eventhandler_<VERSION>_<ARCHITECTURE>.deb

This will create the `etoa` user if necessary and the following files and directories:

 * `/usr/local/bin/etoad` Etoad binary
 * `/usr/local/bin/etoad-manager` Etoad instance manager tool
 * `/etc/init.d/etoad-manager` Init-Script for etoad-manager
 * `/var/run/etoad/` PID files
 * `/var/log/etoad/` Per-instance log files
 * `/etc/etoad/instances-available/` Available instance configurations
 * `/etc/etoad/instances-enabled/` Enabled instance configurations (symlinks)
 
The `etoad-manager` tool simplifies managing multiple etoad instances. To get a list of all availabe configurations, execute:

	etoad-manager list
	
You can then use the `etoad-manager enable <INSTANCE>` command to enable an instance. Start all enabled instances by executing:

	etoad-manager start
	
and track the status of all instances by executing:

	etoad-manager status
	
Execute `etoad-manager` without arguments to get a list of all available actions.

To start etoad-manager automatically on system boot execute

	update-rc.d etoad-manager defaults

### Logging ###

Etoad sends its log messages to the default Unix `logger`. The messages will appear in the system 
log file (e.g. `/var/log/messages`). 
To collect the messages in a dedicated logfile, you can use the following rules (assuming you use Syslog-NG):

	destination d_etoad { file("/var/log/etoad.log" owner(etoa) group(etoa) perm(0644)); };
	filter f_etoad { program("etoad.*"); };
	log { source(s_src); filter(f_etoad); destination(d_etoad); };

Etoad presents itself to the logger with the name `etoad.<INSTANCE>`, you can therefore also create per-instance logfiles:

	destination d_etoad_<INSTANCE> { file("/var/log/etoad/<INSTANCE>.log" owner(etoa) group(etoa) perm(0644) dir_perm(0755) create_dirs(yes)); };
	filter f_etoad_<INSTANCE> { program("etoad.<INSTANCE>"); };
	log { source(s_src); filter(f_etoad_<INSTANCE>); destination(d_etoad_<INSTANCE>); };

Remember to replace `<INSTANCE>` with the name of your instance! The files will be stored in the directory `/var/log/etoad/`.


Authors and Contributors
------------------------

 * Glaubinix
 * River
 * MrCage
