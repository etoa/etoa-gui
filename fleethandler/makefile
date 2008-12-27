OBJECTS = etoa.o FleetHandler.o Fleet.o ConfigHandler.o Functions.o AllianceHandler.o AnalyzeHandler.o AntraxHandler.o AsteroidHandler.o AttackHandler.o BombardHandler.o CancelHandler.o ColonializeHandler.o DebrisHandler.o DefaultHandler.o  DeliveryHandler.o EmpHandler.o ExploreHandler.o FetchHandler.o GasHandler.o GattackHandler.o InvadeHandler.o MarketHandler.o NebulaHandler.o PositionHandler.o ReturnHandler.o SpyHandler.o StealHandler.o StealthHandler.o SupportHandler.o TransportHandler.o WreckageHandler.o BattleHandler.o FightObjectHandler.o ShowObjectHandler.o UserHandler.o DivisionHandler.o  ObjectDataHandler.o ObjectHandler.o DataHandler.o Data.o ShipData.o DefData.o TechData.o BuildingData.o Entity.o Asteroid.o Base.o Empty.o Market.o Nebula.o Planet.o Star.o Unknown.o Wormhole.o Object.o ListDef.o ListShip.o FleetShip.o Message.o Log.o User.o

OPTS = -lmysqlpp

etoa: ${OBJECTS}
	g++ ${OBJECTS} ${OPTS} -o etoa
	
etoa.o: etoa.cpp

	g++ -c etoa.cpp
FleetHandler.o: FleetHandler.cpp FleetHandler.h
	g++ -c FleetHandler.cpp

Fleet.o: objects/Fleet.cpp objects/Fleet.h
	g++ -c objects/Fleet.cpp

ConfigHandler.o: config/ConfigHandler.cpp config/ConfigHandler.h
	g++ -c config/ConfigHandler.cpp

Functions.o: functions/Functions.cpp functions/Functions.h
	g++ -c functions/Functions.cpp

AllianceHandler.o: fleetActions/alliance/AllianceHandler.cpp fleetActions/alliance/AllianceHandler.h
	g++ -c fleetActions/alliance/AllianceHandler.cpp

AnalyzeHandler.o: fleetActions/analyze/AnalyzeHandler.cpp fleetActions/analyze/AnalyzeHandler.h
	g++ -c fleetActions/analyze/AnalyzeHandler.cpp

AntraxHandler.o: fleetActions/antrax/AntraxHandler.cpp fleetActions/antrax/AntraxHandler.h
	g++ -c fleetActions/antrax/AntraxHandler.cpp
	
AsteroidHandler.o: fleetActions/asteroid/AsteroidHandler.cpp fleetActions/asteroid/AsteroidHandler.h
	g++ -c fleetActions/asteroid/AsteroidHandler.cpp

AttackHandler.o: fleetActions/attack/AttackHandler.cpp fleetActions/attack/AttackHandler.h
	g++ -c fleetActions/attack/AttackHandler.cpp

BombardHandler.o: fleetActions/bombard/BombardHandler.cpp fleetActions/bombard/BombardHandler.h
	g++ -c fleetActions/bombard/BombardHandler.cpp

CancelHandler.o: fleetActions/cancel/CancelHandler.cpp fleetActions/cancel/CancelHandler.h
	g++ -c fleetActions/cancel/CancelHandler.cpp

ColonializeHandler.o: fleetActions/colonialize/ColonializeHandler.cpp fleetActions/colonialize/ColonializeHandler.h
	g++ -c fleetActions/colonialize/ColonializeHandler.cpp

DebrisHandler.o: fleetActions/debris/DebrisHandler.cpp fleetActions/debris/DebrisHandler.h
	g++ -c fleetActions/debris/DebrisHandler.cpp
	
DefaultHandler.o: fleetActions/default/DefaultHandler.cpp fleetActions/default/DefaultHandler.h
	g++ -c fleetActions/default/DefaultHandler.cpp	
	
DeliveryHandler.o: fleetActions/delivery/DeliveryHandler.cpp fleetActions/delivery/DeliveryHandler.h
	g++ -c fleetActions/delivery/DeliveryHandler.cpp	
	
EmpHandler.o: fleetActions/emp/EmpHandler.cpp fleetActions/emp/EmpHandler.h
	g++ -c fleetActions/emp/EmpHandler.cpp	

ExploreHandler.o: fleetActions/explore/ExploreHandler.cpp fleetActions/explore/ExploreHandler.h
	g++ -c fleetActions/explore/ExploreHandler.cpp
	
FetchHandler.o: fleetActions/fetch/FetchHandler.cpp fleetActions/fetch/FetchHandler.h
	g++ -c fleetActions/fetch/FetchHandler.cpp

GasHandler.o: fleetActions/gas/GasHandler.cpp fleetActions/gas/GasHandler.h
	g++ -c fleetActions/gas/GasHandler.cpp

GattackHandler.o: fleetActions/gattack/GattackHandler.cpp fleetActions/gattack/GattackHandler.h
	g++ -c fleetActions/gattack/GattackHandler.cpp

InvadeHandler.o: fleetActions/invade/InvadeHandler.cpp fleetActions/invade/InvadeHandler.h
	g++ -c fleetActions/invade/InvadeHandler.cpp

MarketHandler.o: fleetActions/market/MarketHandler.cpp fleetActions/market/MarketHandler.h
	g++ -c fleetActions/market/MarketHandler.cpp

NebulaHandler.o: fleetActions/nebula/NebulaHandler.cpp fleetActions/nebula/NebulaHandler.h
	g++ -c fleetActions/nebula/NebulaHandler.cpp

PositionHandler.o: fleetActions/position/PositionHandler.cpp fleetActions/position/PositionHandler.h
	g++ -c fleetActions/position/PositionHandler.cpp

ReturnHandler.o: fleetActions/return/ReturnHandler.cpp fleetActions/return/ReturnHandler.h
	g++ -c fleetActions/return/ReturnHandler.cpp

SpyHandler.o: fleetActions/spy/SpyHandler.cpp fleetActions/spy/SpyHandler.h
	g++ -c fleetActions/spy/SpyHandler.cpp

StealHandler.o: fleetActions/steal/StealHandler.cpp fleetActions/steal/StealHandler.h
	g++ -c fleetActions/steal/StealHandler.cpp

StealthHandler.o: fleetActions/stealth/StealthHandler.cpp fleetActions/stealth/StealthHandler.h
	g++ -c fleetActions/stealth/StealthHandler.cpp

SupportHandler.o: fleetActions/support/SupportHandler.cpp fleetActions/support/SupportHandler.h
	g++ -c fleetActions/support/SupportHandler.cpp

TransportHandler.o: fleetActions/transport/TransportHandler.cpp fleetActions/transport/TransportHandler.h
	g++ -c fleetActions/transport/TransportHandler.cpp

WreckageHandler.o: fleetActions/wreckage/WreckageHandler.cpp fleetActions/wreckage/WreckageHandler.h
	g++ -c fleetActions/wreckage/WreckageHandler.cpp

BattleHandler.o: battle/BattleHandler.cpp battle/BattleHandler.h
	g++ -c battle/BattleHandler.cpp

FightObjectHandler.o: battle/FightObjectHandler.cpp battle/FightObjectHandler.h
	g++ -c battle/FightObjectHandler.cpp

ShowObjectHandler.o: battle/ShowObjectHandler.cpp battle/ShowObjectHandler.h
	g++ -c battle/ShowObjectHandler.cpp

UserHandler.o: battle/UserHandler.cpp battle/UserHandler.h
	g++ -c battle/UserHandler.cpp

DivisionHandler.o: battle/DivisionHandler.cpp battle/DivisionHandler.h
	g++ -c battle/DivisionHandler.cpp

ObjectDataHandler.o: objectData/ObjectDataHandler.cpp objectData/ObjectDataHandler.h
	g++ -c objectData/ObjectDataHandler.cpp

ObjectHandler.o: objectData/ObjectHandler.cpp objectData/ObjectHandler.h
	g++ -c objectData/ObjectHandler.cpp

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