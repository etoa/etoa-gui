
#ifndef __DEBRISHANDLER__
#define __DEBRISHANDLER__

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Debris....
* Creates a debris field or add the resources to an existing field. The whole fleet will be destroyed
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace debris
{
	class DebrisHandler	: public FleetHandler
	{
	public:
		DebrisHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};

}
#endif
