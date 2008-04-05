
#ifndef __EXPLOREHANDLER__
#define __EXPLOREHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Explore....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace explore
{
	class ExploreHandler	: FleetHandler
	{
	public:
		ExplreHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		double destroy;
		
	};
}
#endif
