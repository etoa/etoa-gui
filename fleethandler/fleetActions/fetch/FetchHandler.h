
#ifndef __FETCHHANDLER__
#define __FETCHHANDLER__

#include <math.h>

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Fetch....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace fetch
{
	class FetchHandler	: public FleetHandler
	{
	public:
		FetchHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};

}
#endif
