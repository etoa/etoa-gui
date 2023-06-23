#!/bin/bash

cd $(dirname $0)/../

if [ $# -ne 1 ]; then
	echo "Usage: $(basename $0) VERSION"
	exit 1
fi

VERSION=$1

sed "s/#define __ETOAD_VERSION_STRING__ \".*\"/#define __ETOAD_VERSION_STRING__ \"${VERSION}\"/" -i eventhandler/src/version.h
sed "s/define('APP_VERSION', '.*');/define('APP_VERSION', '${VERSION}');/" -i htdocs/version.php
