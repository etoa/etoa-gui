#!/bin/bash

VERSION_FILE="src/version.h"

if [ $# -ne 1 ]; then
	echo "Usage: $(basename $0) <VERSION>"
	echo "Sets a new version number in the version.h file"
	exit 1
fi

NEW_VERSION=$1
sed "s/__ETOAD_VERSION_STRING__ \"\(.*\)\"/__ETOAD_VERSION_STRING__ \"${NEW_VERSION}\"/" -i ${VERSION_FILE}
