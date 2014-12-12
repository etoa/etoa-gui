
#ifndef __FLEETFACTORY__
#define __FLEETFACTORY__


/**
* FleetFactoryClass
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

#include "../config/ConfigHandler.h"
#include "fleetActions/FleetAction.h"

#include "fleetActions/AnalyzeHandler.h"
#include "fleetActions/AntraxHandler.h"
#include "fleetActions/AsteroidHandler.h"
#include "fleetActions/AttackHandler.h"
#include "fleetActions/BombardHandler.h"
#include "fleetActions/CancelHandler.h"
#include "fleetActions/ColonializeHandler.h"
#include "fleetActions/DebrisHandler.h"
#include "fleetActions/DefaultHandler.h"
#include "fleetActions/EmpHandler.h"
#include "fleetActions/ExploreHandler.h"
#include "fleetActions/FetchHandler.h"
#include "fleetActions/GasHandler.h"
#include "fleetActions/GattackHandler.h"
#include "fleetActions/InvadeHandler.h"
#include "fleetActions/MarketDeliveryHandler.h"
#include "fleetActions/NebulaHandler.h"
#include "fleetActions/PositionHandler.h"
#include "fleetActions/ReturnHandler.h"
#include "fleetActions/SpyHandler.h"
#include "fleetActions/StealHandler.h"
#include "fleetActions/StealthHandler.h"
#include "fleetActions/SupportHandler.h"
#include "fleetActions/TransportHandler.h"
#include "fleetActions/WreckageHandler.h"
#include "fleetActions/DeliveryHandler.h"
#include "fleetActions/AllianceHandler.h"

class FleetFactory 
{
	public:
	static FleetAction* createFleet(short status, std::string action, mysqlpp::Row fRow) 
	{
		Config &config = Config::instance();
		switch (status)
		{
			case 0:
				switch (config.getAction(action))
				{
					case 1:
						return new analyze::AnalyzeHandler(fRow);
						break;
					case 2:
						return new antrax::AntraxHandler(fRow);
						break;
					case 3:
						return new attack::AttackHandler(fRow);
						break;
					case 4:
						return new bombard::BombardHandler(fRow);
						break;
					case 5:
						return new asteroid::AsteroidHandler(fRow);
						break;
					case 6:
						return new nebula::NebulaHandler(fRow);
						break;
					case 7:
						return new wreckage::WreckageHandler(fRow);
						break;
					case 8:
						return new gas::GasHandler(fRow);
						break;
					case 9:
						return new colonialize::ColonializeHandler(fRow);
						break;
					case 10:
						return new debris::DebrisHandler(fRow);
						break;
					case 11:
						return new delivery::DeliveryHandler(fRow);
						break;
					case 12:
						return new emp::EmpHandler(fRow);
						break;
					case 13:
						return new explore::ExploreHandler(fRow);
						break;
					case 14:
						return new fetch::FetchHandler(fRow);
						break;
					case 15:
						return new gattack::GattackHandler(fRow);
						break;
					case 16:
						return new invade::InvadeHandler(fRow);
						break;
					case 17:
						return new marketdelivery::MarketDeliveryHandler(fRow);
						break;
					case 18:
						return new position::PositionHandler(fRow);
						break;
					case 19:
						return new spy::SpyHandler(fRow);
						break;
					case 20:
						return new steal::StealHandler(fRow);
						break;
					case 21:
						return new stealth::StealthHandler(fRow);
						break;
					case 22:
						return new support::SupportHandler(fRow);
						break;
					case 23:
						return new transport::TransportHandler(fRow);
						break;
					case 24:
						return new alliance::AllianceHandler(fRow);				
						break;
					default:
						return new defaul::DefaultHandler(fRow);
				}
				break;
			case 1:
				return new retour::ReturnHandler(fRow);
				break;
			case 2:
				return new cancel::CancelHandler(fRow);
				break;
			case 3:
				if (action=="support")
					return new support::SupportHandler(fRow);
				break;
			default:
				return new defaul::DefaultHandler(fRow);
		}	
		std::cerr << "Problem mit Flottenaktion! Keine passende Aktion gefunden!" <<std::endl;
		return new defaul::DefaultHandler(fRow);
	}
};
#endif
