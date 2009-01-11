
#ifndef __APOINTSHANDLER__
#define __APOINTSHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

/**
* Handles AllianzShippoints updates
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace aPoints
{
	class aPointsHandler	: EventHandler
	{
	public:
		aPointsHandler()  : EventHandler() { }
		void update();
	};
}
#endif
