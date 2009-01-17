
#ifndef __APOINTSHANDLER__
#define __APOINTSHANDLER__

#include <mysql++/mysql++.h>
#include <ctime>

#include "../EventHandler.h"
#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"

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
