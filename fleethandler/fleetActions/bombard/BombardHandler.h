
#ifndef __BOMBARDHANDLER__
#define __BOMBARDHANDLER__

#include <ctime>
#include <math.h>

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"
#include "../../battle/BattleHandler.h"

/**
* Handles Bombard....
* Levels down a building by random
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace bombard
{
	class BombardHandler	: public FleetHandler
	{
	public:
		BombardHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;

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
		
		/**
		* Entity user id
		**/
		int userToId;
		
	};
}
#endif
