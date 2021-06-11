
#ifndef __ATECHHANDLER__
#define __ATECHHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>
#include <ctime>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

/**
* Handles technology research updates
*
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace atech
{
	class aTechHandler	: EventHandler
	{
	public:
		aTechHandler()  : EventHandler() { }
		~aTechHandler() {}
		void update();
	};
}
#endif
