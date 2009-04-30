SET(UTIL_SRC_FILES
	ExceptionHandler.cpp
	Functions.h
	Functions.cpp
	IPCMessageQueue.cpp
	Logger.cpp
	Mutex.cpp
	PidFile.cpp
)

ADD_LIBRARY(util ${UTIL_SRC_FILES})
