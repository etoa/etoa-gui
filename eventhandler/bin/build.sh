#!/bin/bash

cd $(dirname $0)/..

mkdir -p build
cd build
if [ ! -e Makefile ]; then 
	cmake ..
fi
make $@

