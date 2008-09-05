OBJECTS = etoa.o FleetHandler.o ConfigHandler.o Functions.o AnalyzeHandler.o AntraxHandler.o AsteroidHandler.o AttackHandler.o BombardHandler.o CancelHandler.o ColonializeHandler.o DebrisHandler.o DefaultHandler.o  EmpHandler.o ExploreHandler.o FetchHandler.o GasHandler.o GattackHandler.o InvadeHandler.o MarketHandler.o NebulaHandler.o PositionHandler.o ReturnHandler.o SpyHandler.o StealHandler.o StealthHandler.o SupportHandler.o TransportHandler.o WreckageHandler.o BattleHandler.o FightObjectHandler.o ShowObjectHandler.o UserHandler.o DivisionHandler.o  ObjectDataHandler.o ObjectHandler.o

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

ObjectDataHandler.o: ObjectData/ObjectDataHandler.cpp ObjectData/ObjectDataHandler.h
	g++ -c ObjectData/ObjectDataHandler.cpp

ObjectHandler.o: ObjectData/ObjectHandler.cpp ObjectData/ObjectHandler.h
	g++ -c ObjectData/ObjectHandler.cpp