
#ifndef __DEFAULTHANDLER__
#define __DEFAULTHANDLER_

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Default....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace defaul
{
	class DefaultHandler	: FleetHandler
	{
	public:
		DefaultHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};
}
#endif
