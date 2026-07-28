# Locates mysql++ and the mysql client library it needs.
#
# Sets MYSQLPP_INCLUDE_DIR, MYSQLPP_LIBRARY, MYSQLCLIENT_LIBRARY and MYSQLPP_FOUND.

FIND_PATH(MYSQLPP_INCLUDE_DIR mysql++.h
	PATHS /usr/include/mysql++ /usr/local/include/mysql++)

FIND_LIBRARY(MYSQLPP_LIBRARY NAMES mysqlpp
	PATHS /lib /usr/lib /usr/local/lib)

FIND_LIBRARY(MYSQLCLIENT_LIBRARY NAMES mysqlclient
	PATHS /lib /usr/lib /usr/local/lib /usr/local/mysql/lib)

# Reports what is missing and aborts when the package was requested as REQUIRED.
# The hand written checks this replaced tested "Foo_FIND_REQUIRED", which never
# exists, so a missing library silently produced a broken link instead.
INCLUDE(FindPackageHandleStandardArgs)
FIND_PACKAGE_HANDLE_STANDARD_ARGS(Mysqlpp
	REQUIRED_VARS MYSQLPP_LIBRARY MYSQLPP_INCLUDE_DIR MYSQLCLIENT_LIBRARY)

MARK_AS_ADVANCED(MYSQLPP_INCLUDE_DIR MYSQLPP_LIBRARY MYSQLCLIENT_LIBRARY)
