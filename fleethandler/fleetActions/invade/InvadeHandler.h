
#ifndef __INVADEHANDLER__
#define __INVADEHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Invade....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace invade
{
	class InvadeHandler	: FleetHandler
	{
	public:
		InvadeHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;

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
