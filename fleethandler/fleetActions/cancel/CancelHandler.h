
#ifndef __CANCELHANDLER__
#define __CANCELHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Cancel....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace cancel
{
	class CancelHandler	: FleetHandler
	{
	public:
		CancelHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		
	};
}
#endif
