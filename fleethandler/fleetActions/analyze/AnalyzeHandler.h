
#ifndef __ANALYZEHANDLER__
#define __ANALYZEHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Analyze....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace analyze
{
	class AnalyzeHandler	: FleetHandler
	{
	public:
		AnalyzeHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	private:
		double shipDestroy, destroy;
		int userToId;
		int fuel;
		
	};
}
#endif
