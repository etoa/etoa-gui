
#ifndef __DELIVERYHANDLER__
#define __DELIVERYHANDLER__

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Delivery....
* For alliance deliverys, only ships
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace delivery
{
	class DeliveryHandler	: public FleetHandler
	{
	public:
		DeliveryHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};
}
#endif
