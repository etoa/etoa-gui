SET(UTIL_SRC_FILES
	ExceptionHandler.cpp
	IPCMessageQueue.cpp
	Logger.cpp
	Mutex.cpp
	PidFile.cpp
	functions.h
	sigsegv.c
)

ADD_LIBRARY(util ${UTIL_SRC_FILES})

