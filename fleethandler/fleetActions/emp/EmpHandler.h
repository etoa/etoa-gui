
#ifndef __EMPHANDLER__
#define __EMPHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Emp....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace emp
{
	class EmpHandler	: FleetHandler
	{
	public:
		EmpHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		bool sendMsg;
		
	};
}
#endif
