
#ifndef __NEBULAHANDLER__
#define __NEBULAHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Nebula....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace nebula
{
	class NebulaHandler	: FleetHandler
	{
	public:
		NebulaHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	};
}
#endif
