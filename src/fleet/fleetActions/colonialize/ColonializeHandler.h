
#ifndef __COLONIALIZEHANDLER__
#define __COLONIALIZEHANDLER__

#include "../FleetAction.h"
#include "../../../config/ConfigHandler.h"

/**
* Handles Colonialize....
* You need it to take a new planet, works if the planet doesnt belong to an other user, every object was on the planet will be deleted
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace colonialize
{
	class ColonializeHandler	: public FleetAction
	{
	public:
		ColonializeHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	};

}
#endif
