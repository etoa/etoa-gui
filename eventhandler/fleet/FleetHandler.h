
#ifndef __FLEETHANDLER__
#define __FLEETHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"

namespace fleet
{
	class FleetHandler : EventHandler
	{
	public:
		FleetHandler() : EventHandler() { }		
		void update();
		
		
	};
}
#endif
