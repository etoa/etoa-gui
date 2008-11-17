
#ifndef __DELIVERYHANDLER__
#define __DELIVERYHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Delivery....
* For alliance deliverys, only ships
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace delivery
{
	class DeliveryHandler	: FleetHandler
	{
	public:
		DeliveryHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};
}
#endif
