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
ADD_SUBDIRECTORY(reports)


SET( ETOAD_SRC_FILES
	EventHandler.h
	MysqlHandler.h
	version.h
	etoa.cpp
	main.cpp
)

ADD_EXECUTABLE( etoad ${ETOAD_SRC_FILES} )

TARGET_LINK_LIBRARIES( etoad
  util
  anyoption
	alliance
	building
	config
	data
	def
	entity
	fleet
	market
	objects
	planet
	ship
	tech
	reports
  ${Boost_LIBRARIES}
  ${MYSQLCLIENT_LIBRARY}
  ${MYSQLPP_LIBRARY}
)


INSTALL(TARGETS etoad
  RUNTIME DESTINATION .
)



