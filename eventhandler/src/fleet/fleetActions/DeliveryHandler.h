
#ifndef __DELIVERYHANDLER__
#define __DELIVERYHANDLER__

#include "FleetAction.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Delivery....
* For alliance deliverys, only ships
*
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace delivery
{
	class DeliveryHandler	: public FleetAction
	{
	public:
		DeliveryHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	};
}
#endif
