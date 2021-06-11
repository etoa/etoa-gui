
#ifndef __FETCHHANDLER__
#define __FETCHHANDLER__

#include <math.h>

#include "FleetAction.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Fetch....
*
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace fetch
{
	class FetchHandler	: public FleetAction
	{
	public:
		FetchHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	};

}
#endif
