
#ifndef __AEXPLOREHANDLER__
#define __AEXPLOREHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles explorer....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace explore
{
	class ExploreHandler	: FleetHandler
	{
	public:
		ExploreHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		std::string event();
		int absX, absY, pos;
		double sxNum, cxNum, syNum, cyNum;
		int one, two;
		double days;
	};
}
#endif
