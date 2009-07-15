
#ifndef __FLEETHANDLER__
#define __FLEETHANDLER__

#include <iostream>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../EventHandler.h"
#include "fleetActions/FleetFactory.h"

namespace fleet
{
	class FleetHandler : EventHandler
	{
	public:
		FleetHandler() : EventHandler() { }		
		~FleetHandler() {};
		void update();
		
		
	};
}
#endif
