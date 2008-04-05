
#ifndef __ANTRAXHANDLER__
#define __ANTRAXHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Antrax....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace antrax
{
	class AntraxHandler	: FleetHandler
	{
	public:
		AntraxHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

		
	};
}
#endif
