
#ifndef __SPYHANDLER__
#define __SPYHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Spy....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace spy
{
	class SpyHandler	: FleetHandler
	{
	public:
		SpyHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		bool sendMsg;
		
	};
}
#endif
