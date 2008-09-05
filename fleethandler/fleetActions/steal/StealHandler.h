
#ifndef __STEALHANDLER__
#define __STEALHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Steal....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace steal
{
	class StealHandler	: FleetHandler
	{
	public:
		StealHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
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
		
		/**
		* Entity user id
		**/
		int userToId;
	};
}
#endif
