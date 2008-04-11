
#ifndef __INVADEHANDLER__
#define __INVADEHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Invade....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace invade
{
	class InvadeHandler	: FleetHandler
	{
	public:
		InvadeHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

		
	};
}
#endif
