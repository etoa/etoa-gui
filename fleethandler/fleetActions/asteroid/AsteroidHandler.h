
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
		
	private:
		double fleetCapa, asteroidCapa, capa;
		double goOrNot;
		int asteroid;
		int metal, crystal, plastic, sum;
		int newMetal, newCrystal, newPlastic;
		int one, two;
		double shipDestroy, destroy;
		std::string destroyedShips,destroyedShipsMsg;
		
	};
}
#endif
