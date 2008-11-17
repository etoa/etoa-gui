
#ifndef __MARKETHANDLER__
#define __MARKETHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Market....
* For market deliverys, ships and resources
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace market
{
	class MarketHandler	: FleetHandler
	{
	public:
		MarketHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	
	private:
		/**
		* Defines wheter there are ships or only resources to deliver
		**/
		int landAction;
	};
}
#endif
