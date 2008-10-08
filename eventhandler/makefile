OBJECTS = etoa.o FleetHandler.o BuildingHandler.o TechHandler.o ShipHandler.o ShipList.o DefHandler.o DefList.o Planet.o PlanetManager.o Functions.o ConfigHandler.o MarketHandler.o aTechHandler.o aBuildingHandler.o aPointsHandler.o

OPTS = -lmysqlpp #-lmysqlclient

etoa: ${OBJECTS}
	g++ ${OBJECTS} ${OPTS} -o etoa
	
etoa.o: etoa.cpp

	g++ -c etoa.cpp
FleetHandler.o: fleet/FleetHandler.cpp fleet/FleetHandler.h
	g++ -c fleet/FleetHandler.cpp
	
BuildingHandler.o: building/BuildingHandler.cpp building/BuildingHandler.h
	g++ -c building/BuildingHandler.cpp
	
TechHandler.o: tech/TechHandler.cpp tech/TechHandler.h
	g++ -c tech/TechHandler.cpp	
	
ShipHandler.o: ship/ShipHandler.cpp ship/ShipHandler.h ship/ShipList.h ship/ShipList.cpp
	g++ -c ship/ShipList.cpp ship/ShipHandler.cpp
	
DefHandler.o: def/DefHandler.cpp def/DefHandler.h def/DefList.h def/DefList.cpp
	g++ -c def/DefHandler.cpp def/DefList.cpp

PlanetManager.o: planet/PlanetManager.cpp planet/PlanetManager.h
	g++ -c planet/PlanetManager.cpp	 

Planet.o: planet/Planet.cpp planet/Planet.h
	g++ -c planet/Planet.cpp

Functions.o: functions/Functions.cpp functions/Functions.h
	g++ -c functions/Functions.cpp

ConfigHandler.o: config/ConfigHandler.cpp config/ConfigHandler.h
	g++ -c config/ConfigHandler.cpp

MarketHandler.o: market/MarketHandler.cpp market/MarketHandler.h
	g++ -c market/MarketHandler.cpp

aTechHandler.o: alliance/aTechHandler.cpp alliance/aTechHandler.h
	g++ -c alliance/aTechHandler.cpp

aBuildingHandler.o: alliance/aBuildingHandler.cpp alliance/aBuildingHandler.h
	g++ -c alliance/aBuildingHandler.cpp

aPointsHandler.o: alliance/aPointsHandler.cpp alliance/aPointsHandler.h
	g++ -c alliance/aPointsHandler.cpp

#QuestHandler.o: quest/QuestHandler.cpp quest/QuestHandler.h
#	g++ -c quest/QuestHandler.cpp
 