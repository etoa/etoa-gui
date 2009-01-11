
#ifndef __BOMBARDHANDLER__
#define __BOMBARDHANDLER__

#include <ctime>
#include <math.h>

#include "../FleetAction.h"
#include "../../../config/ConfigHandler.h"
#include "../../battle/BattleHandler.h"

/**
* Handles Bombard....
* Levels down a building by random
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace bombard
{
	class BombardHandler	: public FleetAction
	{
	public:
		BombardHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
		
	private:
		/**
		* Ship they are able to bomb a planet
		**/
		int shipCnt;
		
		/**
		* Bomb tech level
		**/
		short tLevel;

		/**
		* 2 variables to calculate the possibility
		**/
		double one, two;
		
		/**
		* Variable to calculate the new level of the building
		**/
		short bLevel;
	};
}
#endif
