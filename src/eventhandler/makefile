OBJECTS = etoa.o FleetHandler.o BuildingHandler.o TechHandler.o ShipHandler.o ShipList.o DefHandler.o DefList.o PlanetManager.o Functions.o ConfigHandler.o MarketHandler.o aTechHandler.o aBuildingHandler.o aPointsHandler.o FleetAction.o Fleet.o AllianceHandler.o AnalyzeHandler.o AntraxHandler.o AsteroidHandler.o AttackHandler.o BombardHandler.o CancelHandler.o ColonializeHandler.o DebrisHandler.o DefaultHandler.o  DeliveryHandler.o EmpHandler.o ExploreHandler.o FetchHandler.o GasHandler.o GattackHandler.o InvadeHandler.o MarketDeliveryHandler.o NebulaHandler.o PositionHandler.o ReturnHandler.o SpyHandler.o StealHandler.o StealthHandler.o SupportHandler.o TransportHandler.o WreckageHandler.o BattleHandler.o DataHandler.o Data.o ShipData.o DefData.o TechData.o BuildingData.o Entity.o Asteroid.o Base.o Empty.o Market.o Nebula.o Planet.o Star.o Unknown.o Wormhole.o Object.o ListDef.o ListShip.o FleetShip.o Message.o Log.o User.o

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

FleetAction.o: fleet/fleetActions/FleetAction.cpp fleet/fleetActions/FleetAction.h
	g++ -c fleet/fleetActions/FleetAction.cpp

Fleet.o: objects/Fleet.cpp objects/Fleet.h
	g++ -c objects/Fleet.cpp

AllianceHandler.o: fleet/fleetActions/alliance/AllianceHandler.cpp fleet/fleetActions/alliance/AllianceHandler.h
	g++ -c fleet/fleetActions/alliance/AllianceHandler.cpp

AnalyzeHandler.o: fleet/fleetActions/analyze/AnalyzeHandler.cpp fleet/fleetActions/analyze/AnalyzeHandler.h
	g++ -c fleet/fleetActions/analyze/AnalyzeHandler.cpp

AntraxHandler.o: fleet/fleetActions/antrax/AntraxHandler.cpp fleet/fleetActions/antrax/AntraxHandler.h
	g++ -c fleet/fleetActions/antrax/AntraxHandler.cpp
	
AsteroidHandler.o: fleet/fleetActions/asteroid/AsteroidHandler.cpp fleet/fleetActions/asteroid/AsteroidHandler.h
	g++ -c fleet/fleetActions/asteroid/AsteroidHandler.cpp

AttackHandler.o: fleet/fleetActions/attack/AttackHandler.cpp fleet/fleetActions/attack/AttackHandler.h
	g++ -c fleet/fleetActions/attack/AttackHandler.cpp

BombardHandler.o: fleet/fleetActions/bombard/BombardHandler.cpp fleet/fleetActions/bombard/BombardHandler.h
	g++ -c fleet/fleetActions/bombard/BombardHandler.cpp

CancelHandler.o: fleet/fleetActions/cancel/CancelHandler.cpp fleet/fleetActions/cancel/CancelHandler.h
	g++ -c fleet/fleetActions/cancel/CancelHandler.cpp

ColonializeHandler.o: fleet/fleetActions/colonialize/ColonializeHandler.cpp fleet/fleetActions/colonialize/ColonializeHandler.h
	g++ -c fleet/fleetActions/colonialize/ColonializeHandler.cpp

DebrisHandler.o: fleet/fleetActions/debris/DebrisHandler.cpp fleet/fleetActions/debris/DebrisHandler.h
	g++ -c fleet/fleetActions/debris/DebrisHandler.cpp
	
DefaultHandler.o: fleet/fleetActions/default/DefaultHandler.cpp fleet/fleetActions/default/DefaultHandler.h
	g++ -c fleet/fleetActions/default/DefaultHandler.cpp	
	
DeliveryHandler.o: fleet/fleetActions/delivery/DeliveryHandler.cpp fleet/fleetActions/delivery/DeliveryHandler.h
	g++ -c fleet/fleetActions/delivery/DeliveryHandler.cpp	
	
EmpHandler.o: fleet/fleetActions/emp/EmpHandler.cpp fleet/fleetActions/emp/EmpHandler.h
	g++ -c fleet/fleetActions/emp/EmpHandler.cpp	

ExploreHandler.o: fleet/fleetActions/explore/ExploreHandler.cpp fleet/fleetActions/explore/ExploreHandler.h
	g++ -c fleet/fleetActions/explore/ExploreHandler.cpp
	
FetchHandler.o: fleet/fleetActions/fetch/FetchHandler.cpp fleet/fleetActions/fetch/FetchHandler.h
	g++ -c fleet/fleetActions/fetch/FetchHandler.cpp

GasHandler.o: fleet/fleetActions/gas/GasHandler.cpp fleet/fleetActions/gas/GasHandler.h
	g++ -c fleet/fleetActions/gas/GasHandler.cpp

GattackHandler.o: fleet/fleetActions/gattack/GattackHandler.cpp fleet/fleetActions/gattack/GattackHandler.h
	g++ -c fleet/fleetActions/gattack/GattackHandler.cpp

InvadeHandler.o: fleet/fleetActions/invade/InvadeHandler.cpp fleet/fleetActions/invade/InvadeHandler.h
	g++ -c fleet/fleetActions/invade/InvadeHandler.cpp

MarketDeliveryHandler.o: fleet/fleetActions/marketdelivery/MarketDeliveryHandler.cpp fleet/fleetActions/marketdelivery/MarketDeliveryHandler.h
	g++ -c fleet/fleetActions/marketdelivery/MarketDeliveryHandler.cpp

NebulaHandler.o: fleet/fleetActions/nebula/NebulaHandler.cpp fleet/fleetActions/nebula/NebulaHandler.h
	g++ -c fleet/fleetActions/nebula/NebulaHandler.cpp

PositionHandler.o: fleet/fleetActions/position/PositionHandler.cpp fleet/fleetActions/position/PositionHandler.h
	g++ -c fleet/fleetActions/position/PositionHandler.cpp

ReturnHandler.o: fleet/fleetActions/return/ReturnHandler.cpp fleet/fleetActions/return/ReturnHandler.h
	g++ -c fleet/fleetActions/return/ReturnHandler.cpp

SpyHandler.o: fleet/fleetActions/spy/SpyHandler.cpp fleet/fleetActions/spy/SpyHandler.h
	g++ -c fleet/fleetActions/spy/SpyHandler.cpp

StealHandler.o: fleet/fleetActions/steal/StealHandler.cpp fleet/fleetActions/steal/StealHandler.h
	g++ -c fleet/fleetActions/steal/StealHandler.cpp

StealthHandler.o: fleet/fleetActions/stealth/StealthHandler.cpp fleet/fleetActions/stealth/StealthHandler.h
	g++ -c fleet/fleetActions/stealth/StealthHandler.cpp

SupportHandler.o: fleet/fleetActions/support/SupportHandler.cpp fleet/fleetActions/support/SupportHandler.h
	g++ -c fleet/fleetActions/support/SupportHandler.cpp

TransportHandler.o: fleet/fleetActions/transport/TransportHandler.cpp fleet/fleetActions/transport/TransportHandler.h
	g++ -c fleet/fleetActions/transport/TransportHandler.cpp

WreckageHandler.o: fleet/fleetActions/wreckage/WreckageHandler.cpp fleet/fleetActions/wreckage/WreckageHandler.h
	g++ -c fleet/fleetActions/wreckage/WreckageHandler.cpp

BattleHandler.o: fleet/battle/BattleHandler.cpp fleet/battle/BattleHandler.h
	g++ -c fleet/battle/BattleHandler.cpp

DataHandler.o: data/DataHandler.cpp data/DataHandler.h
	g++ -c data/DataHandler.cpp

Data.o: data/Data.cpp data/Data.h
	g++ -c data/Data.cpp

ShipData.o: data/ShipData.cpp data/ShipData.h
	g++ -c data/ShipData.cpp

DefData.o: data/DefData.cpp data/DefData.h
	g++ -c data/DefData.cpp

TechData.o: data/TechData.cpp data/TechData.h
	g++ -c data/TechData.cpp

BuildingData.o: data/BuildingData.cpp data/BuildingData.h
	g++ -c data/BuildingData.cpp

Entity.o: entity/Entity.cpp entity/Entity.h
	g++ -c entity/Entity.cpp

Asteroid.o: entity/Asteroid.cpp entity/Asteroid.h
	g++ -c entity/Asteroid.cpp

Base.o: entity/Base.cpp entity/Base.h
	g++ -c entity/Base.cpp

Empty.o: entity/Empty.cpp entity/Empty.h
	g++ -c entity/Empty.cpp

Market.o: entity/Market.cpp entity/Market.h
	g++ -c entity/Market.cpp

Nebula.o: entity/Nebula.cpp entity/Nebula.h
	g++ -c entity/Nebula.cpp

Planet.o: entity/Planet.cpp entity/Planet.h
	g++ -c entity/Planet.cpp

Star.o: entity/Star.cpp entity/Star.h
	g++ -c entity/Star.cpp

Unknown.o: entity/Unknown.cpp entity/Unknown.h
	g++ -c entity/Unknown.cpp

Wormhole.o: entity/Wormhole.cpp entity/Wormhole.h
	g++ -c entity/Wormhole.cpp

Object.o: objects/Object.cpp objects/Object.h
	g++ -c objects/Object.cpp

ListDef.o: objects/ListDef.cpp objects/ListDef.h
	g++ -c objects/ListDef.cpp

ListShip.o: objects/ListShip.cpp objects/ListShip.h
	g++ -c objects/ListShip.cpp

FleetShip.o: objects/FleetShip.cpp objects/FleetShip.h
	g++ -c objects/FleetShip.cpp

Message.o: objects/Message.cpp objects/Message.h
	g++ -c objects/Message.cpp

Log.o: objects/Log.cpp objects/Log.h
	g++ -c objects/Log.cpp

User.o: objects/User.cpp objects/User.h
	g++ -c objects/User.cpp

clean:
	rm *.o
cleanevent:
	rm etoa.o BuildingHandler.o TechHandler.o ShipHandler.o ShipList.o DefHandler.o DefList.o PlanetManager.o Functions.o ConfigHandler.o MarketHandler.o aTechHandler.o aBuildingHandler.o 
cleanfleet:
	rm etoa. FleetHandler.o FleetAction.o Functions.o ConfigHandler.o Fleet.o AllianceHandler.o AnalyzeHandler.o AntraxHandler.o AsteroidHandler.o AttackHandler.o BombardHandler.o CancelHandler.o ColonializeHandler.o DebrisHandler.o DefaultHandler.o  DeliveryHandler.o EmpHandler.o ExploreHandler.o FetchHandler.o GasHandler.o GattackHandler.o InvadeHandler.o MarketDeliveryHandler.o NebulaHandler.o PositionHandler.o ReturnHandler.o SpyHandler.o StealHandler.o StealthHandler.o SupportHandler.o TransportHandler.o WreckageHandler.o BattleHandler.o DataHandler.o Data.o ShipData.o DefData.o TechData.o BuildingData.o Entity.o Asteroid.o Base.o Empty.o Market.o Nebula.o Planet.o Star.o Unknown.o Wormhole.o Object.o ListDef.o ListShip.o FleetShip.o Message.o Log.o User.o