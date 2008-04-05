
#ifndef __GASHANDLER__
#define __GASHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Gas....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace gas
{
	class GasHandler	: FleetHandler
	{
	public:
		GasHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		bool sendMsg;
		double destroy,fuel,fuelTotal;
		std::string destroyedShips,destroyedShipsMsg;
		
	};
}
#endif
