SET(FLEET_SRC_FILES
	FleetHandler.cpp
	battle/BattleHandler.cpp
	fleetActions/FleetAction.cpp
	fleetActions/FleetFactory.h
	fleetActions/alliance/AllianceHandler.cpp
	fleetActions/analyze/AnalyzeHandler.cpp
	fleetActions/antrax/AntraxHandler.cpp
	fleetActions/asteroid/AsteroidHandler.cpp
	fleetActions/attack/AttackHandler.cpp
	fleetActions/bombard/BombardHandler.cpp
	fleetActions/cancel/CancelHandler.cpp
	fleetActions/colonialize/ColonializeHandler.cpp
	fleetActions/debris/DebrisHandler.cpp
	fleetActions/default/DefaultHandler.cpp
	fleetActions/delivery/DeliveryHandler.cpp
	fleetActions/emp/EmpHandler.cpp
	fleetActions/explore/ExploreHandler.cpp
	fleetActions/fetch/FetchHandler.cpp
	fleetActions/gas/GasHandler.cpp
	fleetActions/gattack/GattackHandler.cpp
	fleetActions/invade/InvadeHandler.cpp
	fleetActions/marketdelivery/MarketDeliveryHandler.cpp
	fleetActions/nebula/NebulaHandler.cpp
	fleetActions/position/PositionHandler.cpp
	fleetActions/return/ReturnHandler.cpp
	fleetActions/spy/SpyHandler.cpp
	fleetActions/steal/StealHandler.cpp
	fleetActions/stealth/StealthHandler.cpp
	fleetActions/support/SupportHandler.cpp
	fleetActions/transport/TransportHandler.cpp
	fleetActions/wreckage/WreckageHandler.cpp

)

ADD_LIBRARY(fleet ${FLEET_SRC_FILES})

