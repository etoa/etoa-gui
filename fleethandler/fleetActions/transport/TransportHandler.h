
#ifndef __TRANSPORTHANDLER__
#define __TRANSPORTHANDLER__

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Transport....
* Well the word transport explains itself
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace transport
{
	class TransportHandler	: public FleetHandler
	{
	public:
		TransportHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};
}
#endif
