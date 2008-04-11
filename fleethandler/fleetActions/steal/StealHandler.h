
#ifndef __STEALHANDLER__
#define __STEALHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Steal....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace steal
{
	class StealHandler	: FleetHandler
	{
	public:
		StealHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	};
}
#endif
