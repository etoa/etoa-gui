
#ifndef __APOINTSHANDLER__
#define __APOINTSHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
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
		~aPointsHandler() {};
		void update();
	};
}
#endif
