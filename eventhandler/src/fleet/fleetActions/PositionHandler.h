
#ifndef __POSITIONHANDLER__
#define __POSITIONHANDLER__

#include "FleetAction.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Position....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace position
{
	class PositionHandler	: public FleetAction
	{
	public:
		PositionHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	};
}
#endif
