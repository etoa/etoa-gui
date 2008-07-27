
#ifndef __DEBRISHANDLER__
#define __DEBRISHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Debris....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace debris
{
	class DebrisHandler	: FleetHandler
	{
	public:
		DebrisHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	private:
		int cnt;
		double tfMetal, tfCrystal, tfPlastic;
		
	};
}
#endif
