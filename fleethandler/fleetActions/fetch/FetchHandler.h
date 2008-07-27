
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
	class FetchHandler	: FleetHandler
	{
	public:
		FetchHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		double capa, capaCnt;
		double loadPeople;
	};
}
#endif
