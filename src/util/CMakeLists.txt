SET(UTIL_SRC_FILES
	ExceptionHandler.cpp
	Functions.cpp
	IPCMessageQueue.cpp
	Logger.cpp
	Mutex.cpp
	PidFile.cpp
	sigsegv.c
)

ADD_LIBRARY(util ${UTIL_SRC_FILES})

