
#ifndef __SPYHANDLER__
#define __SPYHANDLER__

#include <math.h>
#include <time.h>

#include "../FleetAction.h"
#include "../../../functions/Functions.h"
#include "../../../config/ConfigHandler.h"

/**
* Handles Spy....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace spy
{
	class SpyHandler	: public FleetAction
	{
	public:
		SpyHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();

	private:
		/**
		* Tech level agressor and victim
		**/
		double spyLevelAtt, tarnLevelAtt;
		double spyLevelDef, tarnLevelDef;
		
		/**
		* Spy ships
		**/
		double spyShipsDef, spyShipsAtt;

		/**
		* Different spy defense values
		**/
		double spyDefense, spyDefense1, spyDefense2, tarnDefense;
		bool defended, info, support;
		
		/**
		* Something like a go or not variable
		**/
		double roll;

		
	};
}
#endif
