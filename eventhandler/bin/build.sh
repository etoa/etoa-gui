#!/bin/bash

cd $(dirname $0)/..

mkdir -p build
cd build
if which ninja 2>/dev/null 1>&2; then
	cmake -GNinja ..
else
	cmake ..
fi
cmake --build .
