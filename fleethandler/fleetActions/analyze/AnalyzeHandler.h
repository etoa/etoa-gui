
#ifndef __ANALYZEHANDLER__
#define __ANALYZEHANDLER__

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Analyze....
* You can analyze an entity to find out the resources
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace analyze
{
	class AnalyzeHandler	: public FleetHandler
	{
	public:
		AnalyzeHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) {	}
		void update();

	};
}
#endif
