SET(OBJECTS_SRC_FILES
	Fleet.cpp
	FleetShip.cpp
	ListDef.cpp
	ListShip.cpp
	Log.cpp
	Message.cpp
	Object.cpp
	ObjectFactory.h
	User.cpp
)

ADD_LIBRARY(objects ${OBJECTS_SRC_FILES})

