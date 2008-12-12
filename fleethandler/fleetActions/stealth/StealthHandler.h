
#ifndef __STEALTHHANDLER__
#define __STEALTHHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Stealth....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace stealth
{
	class StealthHandler	: public FleetHandler
	{
	public:
		StealthHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	};
}
#endif
