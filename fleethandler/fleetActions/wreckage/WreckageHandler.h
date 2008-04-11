
#ifndef __WRECKAGEHANDLER__
#define __WRECKAGEHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Wreackage....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace wreckage
{
	class WreckageHandler	: FleetHandler
	{
	public:
		WreckageHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		bool sendMsg;
		
	};
}
#endif
