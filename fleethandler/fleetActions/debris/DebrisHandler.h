
#ifndef __DEBRISHANDLER__
#define __DEBRISHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Debris....
* Creates a debris field or add the resources to an existing field. The whole fleet will be destroyed
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace debris
{
	class DebrisHandler	: FleetHandler
	{
	public:
		DebrisHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;
		
		/**
		* Ship count per ship
		**/
		double shipCnt;
		
		/**
		* Size of the debris field
		**/
		double tfMetal, tfCrystal, tfPlastic;
		
	};
}
#endif
