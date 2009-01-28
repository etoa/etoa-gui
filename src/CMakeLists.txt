INCLUDE_DIRECTORIES(.)
INCLUDE_DIRECTORIES(..)

ADD_SUBDIRECTORY(util)
ADD_SUBDIRECTORY(lib)
ADD_SUBDIRECTORY(config)

ADD_SUBDIRECTORY(alliance)
ADD_SUBDIRECTORY(building)
ADD_SUBDIRECTORY(data)
ADD_SUBDIRECTORY(def)
ADD_SUBDIRECTORY(entity)
ADD_SUBDIRECTORY(fleet)
ADD_SUBDIRECTORY(market)
ADD_SUBDIRECTORY(objects)
ADD_SUBDIRECTORY(planet)
ADD_SUBDIRECTORY(ship)
ADD_SUBDIRECTORY(tech)


SET( ETOAD_SRC_FILES
	EventHandler.h
	MysqlHandler.h
	etoa.cpp
	main.cpp
)

ADD_EXECUTABLE( etoad ${ETOAD_SRC_FILES} )

TARGET_LINK_LIBRARIES( etoad
  util
  anyoption
	config
	alliance
	building
	data
	def
	entity
	fleet
	market
	objects
	planet
	ship
	tech	
  ${Boost_LIBRARIES}
  ${MYSQLPP_LIBRARY}
)



