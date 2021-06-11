
#ifndef __TRANSPORTHANDLER__
#define __TRANSPORTHANDLER__

#include "FleetAction.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Transport....
* Well the word transport explains itself
*
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace transport
{
	class TransportHandler	: public FleetAction
	{
	public:
		TransportHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	};
}
#endif
