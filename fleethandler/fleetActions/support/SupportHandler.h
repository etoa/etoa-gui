
#ifndef __SUPPORTHANDLER__
#define __SUPPORTHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Support....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace support
{
	class SupportHandler	: FleetHandler
	{
	public:
		SupportHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		int flyingHomeTime;
		int actionTime;
		int landtime;
		int entity;
		int fuelPerMinute;
		std::string msg;
		std::string action;
		
	};
}
#endif
