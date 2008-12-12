
#ifndef __ASTEROIDHANDLER__
#define __ASTEROIDHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Asteroid....
* Collecting resources from an asteroidfield
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace asteroid
{
	class AsteroidHandler	: public FleetHandler
	{
	public:
		AsteroidHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		/**
		* different capacitys
		**/
		double fleetCapa, asteroidCapa, capa;
		
		/**
		*Calculated/collected resources
		**/
		double asteroid;
		double metal, crystal, plastic, sum;
		
		/**
		* Resources from the new asteroid field
		**/
		double newMetal, newCrystal, newPlastic;
		
		/**
		* Possibilitys, if the action succed
		**/
		int one, two;
		
		/**
		* Number on percentage ships were destroyed
		**/
		double shipDestroy, destroy;
		
		/**
		* Message for the destroyed ships
		**/
		std::string destroyedShips, destroyedShipsMsg;
		
		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;
		
	};
}
#endif
