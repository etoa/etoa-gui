
#ifndef __GATTACKHANDLER__
#define __GATTACKHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Gas Attack....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace gattack
{
	class GattackHandler	: FleetHandler
	{
	public:
		GattackHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		bool sendMsg;
		
	};
}
#endif
