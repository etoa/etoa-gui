
#ifndef __INVADEHANDLER__
#define __INVADEHANDLER__

#include <ctime>
#include <math.h>

#include "FleetAction.h"
#include "../../util/Functions.h"
#include "../../config/ConfigHandler.h"

#include "../battle/BattleHandler.h"

/**
* Handles Invade....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace invade
{
	class InvadeHandler	: public FleetAction
	{
	public:
		InvadeHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
		
	private:
		/**
		* Ship they are able to invade a planet
		**/
		int shipCnt;
		
		/**
		* defender and agressor points
		**/
		int pointsDef, pointsAtt;

		/**
		* variables to calculate the possibility
		**/
		double chance, one, two;
		
		/**
		* Variables to send planet user fleets to main
		**/
		int duration, launchtime, landtime;
		
		/**
		* TimeHandler
		**/
		std::time_t time;
		
		/**
		* Entity user id
		**/
		int userToId;
		
	};
}
#endif
