
#ifndef __MARKETDELIVERYHANDLER__
#define __MARKETDELIVERYHANDLER__

#include "../FleetAction.h"
#include "../../../config/ConfigHandler.h"

/**
* Handles Market....
* For market deliverys, ships and resources
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace marketdelivery
{
	class MarketDeliveryHandler	: public FleetAction
	{
	public:
		MarketDeliveryHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	
	};
}
#endif
