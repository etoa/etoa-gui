
#ifndef __MARKETHANDLER__
#define __MARKETHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Market....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace market
{
	class MarketHandler	: FleetHandler
	{
	public:
		MarketHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	
	private:
		int landAction;
		int userToId;
	};
}
#endif
