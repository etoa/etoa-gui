
#ifndef __EXPLOREHANDLER__
#define __EXPLOREHANDLER__

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles explorer....
* Important to discover the universe and to get some quest's
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace explore
{
	class ExploreHandler	: public FleetHandler
	{
	public:
		ExploreHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};

}
#endif
