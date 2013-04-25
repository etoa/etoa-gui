SET(QUEUE_SRC_FILES
	IPCMessageQueue.cpp
	EntityUpdateQueue.cpp
)

ADD_LIBRARY(queue ${QUEUE_SRC_FILES})
