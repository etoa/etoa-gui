
#ifndef __EMPHANDLER__
#define __EMPHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Emp....
* Deactivades a building for several hours
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace emp
{
	class EmpHandler	: FleetHandler
	{
	public:
		EmpHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;

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
		int h, time, time2Add;
		
		/**
		* Entity user id
		**/
		int userToId;
	};
}
#endif
