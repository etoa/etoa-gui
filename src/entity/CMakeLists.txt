SET(ENTITY_SRC_FILES
	Asteroid.cpp
	Base.cpp
	Empty.cpp
	Entity.cpp
	EntityFactory.h
	Market.cpp
	Nebula.cpp
	Planet.cpp
	Star.cpp
	Unknown.cpp
	Wormhole.cpp
	
)

ADD_LIBRARY(entity ${ENTITY_SRC_FILES})

