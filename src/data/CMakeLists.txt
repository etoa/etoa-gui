SET(DATA_SRC_FILES
	BuildingData.cpp
	Data.cpp
	DataHandler.cpp
	DefData.cpp
	PlanetData.cpp
	RaceData.cpp
	ShipData.cpp
	SolData.cpp
	SpecialistData.cpp
	TechData.cpp
)

ADD_LIBRARY(data ${DATA_SRC_FILES})

