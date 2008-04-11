
#ifndef __ASTEROIDHANDLER__
#define __ASTEROIDHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Asteroid....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace asteroid
{
	class AsteroidHandler	: FleetHandler
	{
	public:
		AsteroidHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	};
}
#endif
