EtoA Eventhandler (etoad)
=========================

A backend for handling events in the Escape to Andromeda game written in C++.


Requirements
------------

Tools:

 * build-essential
 * cmake

Libraries:

 * libboost-filesystem-dev
 * libboost-regex-dev
 * libboost-system-dev
 * libboost-thread-dev
 * libmysql++-dev

If you use Debian Linux, you can simply execute

	apt-get install build-essential cmake libboost-all-dev libmysql++-dev

to install all dependencies

Building
--------

Execute

	bin/build.sh

to create all Makefiles using Cmake and compile the code.

Releasing
---------

Define release version and next development version:

    VERSION=3.0.2
    DEV_VERSION=3.X.X-SNAPSHOT
    
Create SCM tag:

    bin/set-version.sh $VERSION
    svn commit -m "[Prepare for release]"
    svn cp https://dev.etoa.net/svn/etoa-eventhandler/trunk https://dev.etoa.net/svn/etoa-eventhandler/tags/$VERSION -m "[Release $VERSION]"
    svn switch https://dev.etoa.net/svn/etoa-eventhandler/tags/$VERSION
    
Build source and create debian package:

    bin/build.sh
    bin/make-deb.sh
    
Switch back to trunk:

    svn switch https://dev.etoa.net/svn/etoa-eventhandler/trunk
    bin/set-version.sh $DEV_VERSION
    svn commit -m "[Prepare for next development iteration]"


