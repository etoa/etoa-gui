
#ifndef __COLONIALIZEHANDLER__
#define __COLONIALIZEHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Colonialize....
* You need it to take a new planet, works if the planet doesnt belong to an other user, every object was on the planet will be deleted
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace colonialize
{
	class ColonializeHandler	: FleetHandler
	{
	public:
		ColonializeHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;
		
	};
}
#endif
