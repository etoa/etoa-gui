#!/bin/bash
#
# Create debian package of etoad
# $Id$
#

#
# Config
#

TOP=$(dirname $0)/..
DEB_NAME=etoa-eventhandler
PKG_DIR=$TOP/target
BIN_FILE=$TOP/target/etoad
ARCH=$(uname -m)

#
# Subs
#

cleanup() {
  [ -n "${tdir}" ] && [ -d "${tdir}" ] && rm -rf "${tdir}"
}

#
# Main
#

cd $TOP

# Detect version
VER=$(grep "__ETOAD_VERSION_STRING__" src/version.h | awk '{print $3}' | sed 's/"//g')

# Define package name
pkgname=${DEB_NAME}_${VER}_${ARCH}

# Ensure source has been compiled
if [ ! -f $BIN_FILE ]; then
	echo "Source has not been built! Please execute 'bin/build.sh'"
	exit 1;
fi

tdir=$(mktemp -d)
trap 'cleanup' INT TERM EXIT

# Copy contents of dist/debian/
rsync -au --exclude '.svn' dist/debian/ $tdir
mkdir -p $tdir/usr/bin
cp $BIN_FILE $tdir/usr/bin
strip $tdir/usr/bin/$(basename $BIN_FILE)
cp ${TOP}/dist/etoad-manager $tdir/usr/bin

# Write version to control file
sed "s/^Version: .*/Version: ${VER}/" -i $tdir/DEBIAN/control


if [ "$ARCH" == "x86_64" ]; then
    deb_arch=amd64
else
    deb_arch=$ARCH
fi
sed "s/^Architecture: .*/Architecture: ${deb_arch}/" -i $tdir/DEBIAN/control


# Create package
mkdir -p ${PKG_DIR}
fakeroot dpkg -b $tdir ${PKG_DIR}/${pkgname}.deb

if [ -e /usr/bin/lintian ]; then
    echo "Lintian analysis results:"
    /usr/bin/lintian ${PKG_DIR}/${pkgname}.deb
fi

