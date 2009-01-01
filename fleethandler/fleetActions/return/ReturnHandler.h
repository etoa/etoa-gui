
#ifndef __RETURNHANDLER__
#define __RETURNHANDLER__

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Return....
* After every action the fleet returns to the startplanet, that's it
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace retour
{
	class ReturnHandler	: public FleetHandler
	{
	public:
		ReturnHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	
	};
}
#endif
