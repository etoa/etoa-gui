
#ifndef __GATTACKHANDLER__
#define __GATTACKHANDLER__

#include <ctime>
#include <math.h>

#include "../FleetAction.h"
#include "../../../util/Functions.h"
#include "../../../config/ConfigHandler.h"

#include "../../battle/BattleHandler.h"

/**
* Handles Gas Attack....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace gattack
{
	class GattackHandler	: public FleetAction
	{
	public:
		GattackHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();

	private:
		/**
		* Ship they are able to antrax a planet
		**/
		int shipCnt;
		
		/**
		* Antrax tech level
		**/
		short tLevel;

		/**
		* 2 variables to calculate the possibility
		**/
		double one, two;
		
		/**
		* 2 variables to calculate the damage in percent * 100
		**/
		int temp, fak;
		
		/**
		* Variable to calculate the people lost in the storm, fire or whatever
		**/
		double people;
	};

}
#endif
