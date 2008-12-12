
#ifndef __ANTRAXHANDLER__
#define __ANTRAXHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Antrax....
* Destroy food and people on the planet
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace antrax
{
	class AntraxHandler	: public FleetHandler
	{
	public:
		AntraxHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
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
		* Variables to calculate the damage food an people
		**/
		double people, peopleRest;
		double food, foodRest;
		
		/**
		* Entity user id
		**/
		int userToId;
		
	};
}
#endif
