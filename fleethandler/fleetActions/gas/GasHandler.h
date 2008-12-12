
#ifndef __GASHANDLER__
#define __GASHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Gas....
* Collect gas from a gas planet
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace gas
{
	class GasHandler	: public FleetHandler
	{
	public:
		GasHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:		

		/**
		* Message part for destroyed ships
		**/
		std::string destroyedShips,destroyedShipsMsg;

		/**
		* Variables to calculate the destroyed ships
		**/
		double shipDestroy, destroy;
		
		/**
		* Variables to calculate if there got any ships destroyed
		**/
		int one, two;
				
		/**
		* Variables to calculate the collected fuel
		**/
		double fuel, newFuel, fuelTotal;

		/**
		* Variables to calculate the capacity
		**/
		double gasCapa, fleetCapa, capa;

		
		
	};
}
#endif
