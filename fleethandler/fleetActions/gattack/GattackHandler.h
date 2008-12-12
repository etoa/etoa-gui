
#ifndef __GATTACKHANDLER__
#define __GATTACKHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Gas Attack....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace gattack
{
	class GattackHandler	: public FleetHandler
	{
	public:
		GattackHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
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
		* 2 variables to calculate the damage in percent * 100
		**/
		int temp, fak;
		
		/**
		* Variables to calculate the people lost in the storm, fire or whatever
		**/
		double people, rest;
		
		/**
		* Entity user id
		**/
		int userToId;
	};
}
#endif
