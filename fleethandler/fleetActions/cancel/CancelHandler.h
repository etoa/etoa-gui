
#ifndef __CANCELHANDLER__
#define __CANCELHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Cancel....
* For every action, that failed, was stopped or canceled
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace cancel
{
	class CancelHandler	: public FleetHandler
	{
	public:
		CancelHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};
}
#endif
