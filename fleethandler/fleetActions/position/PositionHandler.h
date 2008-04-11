
#ifndef __POSITIONHANDLER__
#define __POSITIONHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Position....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace position
{
	class PositionHandler	: FleetHandler
	{
	public:
		PositionHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		bool sendMsg;
		
	};
}
#endif
