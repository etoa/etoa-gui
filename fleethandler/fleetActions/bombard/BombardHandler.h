
#ifndef __BOMBARDHANDLER__
#define __BOMBARDHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Attack....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace bombard
{
	class BombardHandler	: FleetHandler
	{
	public:
		BombardHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	};
}
#endif
