
#ifndef __FLEETFACTORY__
#define __FLEETFACTORY__


/**
* FleetFactoryClass
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

#include "../../config/ConfigHandler.h"
#include "FleetAction.h"

#include "analyze/AnalyzeHandler.h"
#include "antrax/AntraxHandler.h"
#include "asteroid/AsteroidHandler.h"
#include "attack/AttackHandler.h"
#include "bombard/BombardHandler.h"
#include "cancel/CancelHandler.h"
#include "colonialize/ColonializeHandler.h"
#include "debris/DebrisHandler.h"
#include "default/DefaultHandler.h"
#include "emp/EmpHandler.h"
#include "explore/ExploreHandler.h"
#include "fetch/FetchHandler.h"
#include "gas/GasHandler.h"
#include "gattack/GattackHandler.h"
#include "invade/InvadeHandler.h"
#include "marketdelivery/MarketDeliveryHandler.h"
#include "nebula/NebulaHandler.h"
#include "position/PositionHandler.h"
#include "return/ReturnHandler.h"
#include "spy/SpyHandler.h"
#include "steal/StealHandler.h"
#include "stealth/StealthHandler.h"
#include "support/SupportHandler.h"
#include "transport/TransportHandler.h"
#include "wreckage/WreckageHandler.h"
#include "delivery/DeliveryHandler.h"
#include "alliance/AllianceHandler.h"

class FleetFactory {
public:

	static FleetAction* createFleet(short status, std::string action, mysqlpp::Row fRow) {
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
	}
};
#endif
