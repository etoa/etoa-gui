SET(UTIL_SRC_FILES
	ExceptionHandler.cpp
	Functions.h
	Functions.cpp
	IPCMessageQueue.cpp
	Mutex.cpp
	PidFile.cpp
	Log.cpp
	Log.h
)

ADD_LIBRARY(util ${UTIL_SRC_FILES})
