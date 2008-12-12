
#ifndef __FETCHHANDLER__
#define __FETCHHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Fetch....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace fetch
{
	class FetchHandler	: public FleetHandler
	{
	public:
		FetchHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;
		/**
		* Several Capacity's to calculate the fetched resources
		**/
		double capa, capaCnt;
		double loadPeople;
	};
}
#endif
