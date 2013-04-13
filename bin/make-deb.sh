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
make

tdir=$(mktemp -d)
trap 'cleanup' INT TERM EXIT

# Copy contents of dist/debian/
rsync -au --exclude '.svn' dist/debian/ $tdir
mkdir -p $tdir/usr/local/bin
cp $BIN_FILE $tdir/usr/local/bin

# Write version to control file
sed "s/^Version: .*/Version: ${VER}/" -i $tdir/DEBIAN/control


# Create package
mkdir -p ${PKG_DIR}
dpkg -b $tdir ${PKG_DIR}/${pkgname}.deb
