
#ifndef __DEBRISHANDLER__
#define __DEBRISHANDLER__

#include "FleetAction.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Debris....
* Creates a debris field or add the resources to an existing field. The whole fleet will be destroyed
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace debris
{
	class DebrisHandler	: public FleetAction
	{
	public:
		DebrisHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	};

}
#endif
