
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
	class NebulaHandler	: public FleetHandler
	{
	public:
		NebulaHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;
		
		/**
		* Variable to calculate if there got some ships destroyed
		**/
		double shipDestroy, destroy;
				
		/**
		* Message part for the destroyed ships
		**/
		std::string destroyedShips, destroyedShipsMsg;
		
		/**
		* Variables to calculate the possibility if the action failed or not
		**/
		int one, two;
		
		/**
		* Different capacitys
		**/
		double nebulaCapa, fleetCapa, capa;
		
		/**
		* Calculated collected nebula (Can be higher then the effectiv 
		**/
		double nebula;
		
		/**
		* Nebula on the planet
		**/
		double maxRess;
		
		/**
		* Effectiv collected nebula
		**/
		double crystal;
		
		/**
		* Crystal at the fleet after collect from the field
		**/
		double resTotal;
		
		/**
		* Resource of the new field
		**/
		double newRess;
		
	};
}
#endif
