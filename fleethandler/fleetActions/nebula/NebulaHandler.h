
#ifndef __NEBULAHANDLER__
#define __NEBULAHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Nebula....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace nebula
{
	class NebulaHandler	: FleetHandler
	{
	public:
		NebulaHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		double nebulaCapa, fleetCapa, capa;
		double goOrNot;
		double nebula;
		double crystal;
		double resTotal;
		double newRess;
		double maxRess;
		int one, two;
		double shipDestroy, destroy;
		std::string destroyedShips,destroyedShipsMsg;
	};
}
#endif
