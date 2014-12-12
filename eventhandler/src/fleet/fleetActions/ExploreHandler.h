
#ifndef __EXPLOREHANDLER__
#define __EXPLOREHANDLER__

#include "FleetAction.h"
#include "../../config/ConfigHandler.h"

#include "../../reports/ExploreReport.h"

/**
* Handles explorer....
* Important to discover the universe and to get some quest's
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace explore
{
	class ExploreHandler	: public FleetAction
	{
	public:
		ExploreHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	};

}
#endif
