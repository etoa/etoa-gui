
#ifndef __ANTRAXHANDLER__
#define __ANTRAXHANDLER__

#include <ctime>
#include <math.h>

#include "FleetAction.h"
#include "../../util/Functions.h"
#include "../../config/ConfigHandler.h"

#include "../battle/BattleHandler.h"

/**
* Handles Antrax....
* Destroy food and people on the planet
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace antrax
{
	class AntraxHandler	: public FleetAction
	{
	public:
		AntraxHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
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
		* Variables to calculate the damage food an people
		**/
		double food, people;
	};

}
#endif
