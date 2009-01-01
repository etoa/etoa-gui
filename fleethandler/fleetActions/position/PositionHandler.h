
#ifndef __POSITIONHANDLER__
#define __POSITIONHANDLER__

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Position....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace position
{
	class PositionHandler	: public FleetHandler
	{
	public:
		PositionHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};
}
#endif
