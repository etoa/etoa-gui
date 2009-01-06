
#ifndef __EMPHANDLER__
#define __EMPHANDLER__

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"
#include "../../battle/BattleHandler.h"

/**
* Handles Emp....
* Deactivades a building for several hours
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace emp
{
	class EmpHandler	: public FleetHandler
	{
	public:
		EmpHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
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
		* Variables to calculate the damage
		**/
		int h;
	};
}
#endif
