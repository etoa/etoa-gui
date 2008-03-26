
#ifndef __FLEETHANDLER__
#define __FLEETHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"

namespace fleet
{
	class FleetHandler : EventHandler
	{
	public:
		FleetHandler(mysqlpp::Connection* con) : EventHandler(con) { }		
		void update();
		
		
	};
}
#endif
