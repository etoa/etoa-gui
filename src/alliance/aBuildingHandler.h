
#ifndef __ABUILDINGHANDLER__
#define __ABUILDINGHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include <ctime>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

/**
* Handles building updates
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace abuilding
{
	class aBuildingHandler	: EventHandler
	{
	public:
		aBuildingHandler()  : EventHandler() { }
		~aBuildingHandler() {};
		void update();
	};
}
#endif
