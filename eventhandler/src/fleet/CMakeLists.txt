SET(FLEET_SRC_FILES
	FleetHandler.cpp
	battle/BattleHandler.cpp
	fleetActions/FleetAction.cpp
	FleetFactory.h
	fleetActions/AllianceHandler.cpp
	fleetActions/AnalyzeHandler.cpp
	fleetActions/AntraxHandler.cpp
	fleetActions/AsteroidHandler.cpp
	fleetActions/AttackHandler.cpp
	fleetActions/BombardHandler.cpp
	fleetActions/CancelHandler.cpp
	fleetActions/ColonializeHandler.cpp
	fleetActions/DebrisHandler.cpp
	fleetActions/DefaultHandler.cpp
	fleetActions/DeliveryHandler.cpp
	fleetActions/EmpHandler.cpp
	fleetActions/ExploreHandler.cpp
	fleetActions/FetchHandler.cpp
	fleetActions/GasHandler.cpp
	fleetActions/GattackHandler.cpp
	fleetActions/InvadeHandler.cpp
	fleetActions/MarketDeliveryHandler.cpp
	fleetActions/NebulaHandler.cpp
	fleetActions/PositionHandler.cpp
	fleetActions/ReturnHandler.cpp
	fleetActions/SpyHandler.cpp
	fleetActions/StealHandler.cpp
	fleetActions/StealthHandler.cpp
	fleetActions/SupportHandler.cpp
	fleetActions/TransportHandler.cpp
	fleetActions/WreckageHandler.cpp

)

ADD_LIBRARY(fleet ${FLEET_SRC_FILES})

TARGET_LINK_LIBRARIES( fleet
  entity
  util
  data
)
