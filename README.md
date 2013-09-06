EtoA Eventhandler (etoad)
=========================

A backend for handling events in the Escape to Andromeda game written in C++.


Requirements
------------

 * Linux C++ build chain
 * CMake
 * Boost library (filesystem, regex, system, thread)
 * MySQL++ library

If you use Debian Linux, you can simply execute

	apt-get install build-essential cmake libboost-all-dev libmysql++-dev

to install all dependencies.

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
    git add src/version.h
    git commit -m "[Release $VERSION]"
    git tag $VERSION
    
Build source and create debian package:

    bin/build.sh
    bin/make-deb.sh
    
Switch back to trunk:

    bin/set-version.sh $DEV_VERSION
    git add src/version.h
    git commit -m "[Prepare for next development iteration $DEV_VERSION]"


