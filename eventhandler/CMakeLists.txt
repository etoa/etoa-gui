PROJECT(EtoABackend)

CMAKE_MINIMUM_REQUIRED(VERSION 2.6)

SET(CMAKE_CXX_STANDARD 11)

SET(CMAKE_MODULE_PATH ${CMAKE_CURRENT_SOURCE_DIR}/cmake)

SET(EXECUTABLE_OUTPUT_PATH ${PROJECT_SOURCE_DIR}/target)
SET(LIBRARY_OUTPUT_PATH    ${PROJECT_SOURCE_DIR}/target/lib)

SET(CMAKE_C_FLAGS "$ENV{CFLAGS} -O3 -Wall -g -ggdb")
SET(CMAKE_CXX_FLAGS "$ENV{CXXFLAGS} -O3 -Wall -g -ggdb")


FIND_PACKAGE(Boost REQUIRED thread regex filesystem system)
FIND_PACKAGE(Mysqlpp REQUIRED)

# MYSQLPP_LIBRARY/MYSQLCLIENT_LIBRARY are the library files themselves and must
# not be added here: as link directories they produced bogus -L and -rpath
# entries and made cmake fall back to "-lmysqlpp" instead of the resolved path.
LINK_DIRECTORIES(
  ${Boost_LIBRARY_DIRS}
)

INCLUDE_DIRECTORIES(
  ${Boost_INCLUDE_DIRS}
  ${MYSQLPP_INCLUDE_DIR}
)


ADD_SUBDIRECTORY(src)

SET(CMAKE_INSTALL_PREFIX "/usr/local/bin")


