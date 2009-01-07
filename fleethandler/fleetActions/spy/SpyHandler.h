
#ifndef __SPYHANDLER__
#define __SPYHANDLER__

#include <math.h>
#include <time.h>

#include "../../FleetHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Spy....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace spy
{
	class SpyHandler	: public FleetHandler
	{
	public:
		SpyHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		/**
		* Tech level agressor and victim
		**/
		short spyLevelAtt, tarnLevelAtt;
		short spyLevelDef, tarnLevelDef;
		
		/**
		* Spy ships
		**/
		int spyShipsDef, spyShipsAtt;

		/**
		* Different spy defense values
		**/
		double spyDefense, spyDefense1, spyDefense2, tarnDefense;
		bool defended, info;
		
		/**
		* Something like a go or not variable
		**/
		double roll;

		
	};
}
#endif
