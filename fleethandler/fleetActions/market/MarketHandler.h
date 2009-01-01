
#ifndef __MARKETHANDLER__
#define __MARKETHANDLER__

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Market....
* For market deliverys, ships and resources
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace market
{
	class MarketHandler	: public FleetHandler
	{
	public:
		MarketHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	
	};
}
#endif
