
#ifndef __STEALTHHANDLER__
#define __STEALTHHANDLER__

#include <math.h>

#include "../FleetAction.h"
#include "../../../functions/Functions.h"
#include "../../../config/ConfigHandler.h"

#include "../../battle/BattleHandler.h"

/**
* Handles Stealth....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace stealth
{
	class StealthHandler	: public FleetAction
	{
	public:
		StealthHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	
	private:
		/**
		* Ship they are able to antrax a planet
		**/
		int shipCnt;
		
		/**
		* Spy tech level
		**/
		short tLevelAtt, tLevelDef;

		/**
		* 2 variables to calculate the possibility
		**/
		double one, two;
	};

}
#endif
