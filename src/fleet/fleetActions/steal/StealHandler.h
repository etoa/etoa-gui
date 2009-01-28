
#ifndef __STEALHANDLER__
#define __STEALHANDLER__

#include "../FleetAction.h"

#include "../../battle/BattleHandler.h"


/**
* Handles Steal....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace steal
{
	class StealHandler	: public FleetAction
	{
	public:
		StealHandler(mysqlpp::Row fleet)  :  FleetAction(fleet) { }
		void update();
		
	private:
		/**
		* Ship they are able to antrax a planet
		**/
		int shipCnt;
		
		/**
		* Spytechs tech level
		**/
		short tLevelAtt, tLevelDef;

		/**
		* 2 variables to calculate the possibility
		**/
		double one, two;
		
		/**
		* 2 variables to calculate the damage in percent * 100
		**/
		int temp, fak;
		
	};
}
#endif
