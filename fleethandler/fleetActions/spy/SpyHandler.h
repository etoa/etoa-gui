
#ifndef __SPYHANDLER__
#define __SPYHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Spy....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace spy
{
	class SpyHandler	: public FleetHandler
	{
	public:
		SpyHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;

		/**
		* Tech level agressor and victim
		**/
		short spyLevelAtt, tarnLevelAtt;
		short spyLevelDef, tarnLevelDef;
		
		/**
		* Spy ships
		**/
		int spyShipsDef, spyShipsAtt;

		/**
		* Different spy defense values
		**/
		double spyDefense, spyDefense1, spyDefense2, tarnDefense;
		bool defended;
		
		/**
		* Something like a go or not variable
		**/
		double roll;
		
		/**
		* Entity user id
		**/
		int userToId;
		
	};
}
#endif
