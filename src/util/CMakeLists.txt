SET(UTIL_SRC_FILES
	ExceptionHandler.cpp
	Functions.h
	Functions.cpp
	Mutex.cpp
	PidFile.cpp
	Log.cpp
	Log.h
	Debug.h
	Debug.cpp
	Chameleon.cpp
	ConfigFile.cpp
	MemInfo.cpp
)

ADD_LIBRARY(util ${UTIL_SRC_FILES})
