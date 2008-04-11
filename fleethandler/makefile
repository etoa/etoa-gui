OBJECTS = etoa.o FleetHandler.o ConfigHandler.o Functions.o CancelHandler.o FetchHandler.o ReturnHandler.o DefaultHandler.o AsteroidHandler.o ColonializeHandler.o ExploreHandler.o GasHandler.o MarketHandler.o NebulaHandler.o PositionHandler.o SpyHandler.o TransportHandler.o WreckageHandler.o DebrisHandler.o
OPTS = -lmysqlpp

etoa: ${OBJECTS}
	g++ ${OBJECTS} ${OPTS} -o etoa
	
etoa.o: etoa.cpp

	g++ -c etoa.cpp
FleetHandler.o: FleetHandler.cpp FleetHandler.h
	g++ -c FleetHandler.cpp

ConfigHandler.o: config/ConfigHandler.cpp config/ConfigHandler.h
	g++ -c config/ConfigHandler.cpp

Functions.o: functions/Functions.cpp functions/Functions.h
	g++ -c functions/Functions.cpp

CancelHandler.o: fleetActions/cancel/CancelHandler.cpp fleetActions/cancel/CancelHandler.h
	g++ -c fleetActions/cancel/CancelHandler.cpp

ReturnHandler.o: fleetActions/return/ReturnHandler.cpp fleetActions/return/ReturnHandler.h
	g++ -c fleetActions/return/ReturnHandler.cpp
	
FetchHandler.o: fleetActions/fetch/FetchHandler.cpp fleetActions/fetch/FetchHandler.h
	g++ -c fleetActions/fetch/FetchHandler.cpp
	
DefaultHandler.o: fleetActions/default/DefaultHandler.cpp fleetActions/default/DefaultHandler.h
	g++ -c fleetActions/default/DefaultHandler.cpp	
	
AsteroidHandler.o: fleetActions/asteroid/AsteroidHandler.cpp fleetActions/asteroid/AsteroidHandler.h
	g++ -c fleetActions/asteroid/AsteroidHandler.cpp

ColonializeHandler.o: fleetActions/colonialize/ColonializeHandler.cpp fleetActions/colonialize/ColonializeHandler.h
	g++ -c fleetActions/colonialize/ColonializeHandler.cpp

ExploreHandler.o: fleetActions/explore/ExploreHandler.cpp fleetActions/explore/ExploreHandler.h
	g++ -c fleetActions/explore/ExploreHandler.cpp

GasHandler.o: fleetActions/gas/GasHandler.cpp fleetActions/gas/GasHandler.h
	g++ -c fleetActions/gas/GasHandler.cpp

MarketHandler.o: fleetActions/market/MarketHandler.cpp fleetActions/market/MarketHandler.h
	g++ -c fleetActions/market/MarketHandler.cpp

NebulaHandler.o: fleetActions/nebula/NebulaHandler.cpp fleetActions/nebula/NebulaHandler.h
	g++ -c fleetActions/nebula/NebulaHandler.cpp

PositionHandler.o: fleetActions/position/PositionHandler.cpp fleetActions/position/PositionHandler.h
	g++ -c fleetActions/position/PositionHandler.cpp

SpyHandler.o: fleetActions/spy/SpyHandler.cpp fleetActions/spy/SpyHandler.h
	g++ -c fleetActions/spy/SpyHandler.cpp

TransportHandler.o: fleetActions/transport/TransportHandler.cpp fleetActions/transport/TransportHandler.h
	g++ -c fleetActions/transport/TransportHandler.cpp

WreckageHandler.o: fleetActions/wreckage/WreckageHandler.cpp fleetActions/wreckage/WreckageHandler.h
	g++ -c fleetActions/wreckage/WreckageHandler.cpp

DebrisHandler.o: fleetActions/debris/DebrisHandler.cpp fleetActions/debris/DebrisHandler.h
	g++ -c fleetActions/debris/DebrisHandler.cpp