
#ifndef __ANALYZEHANDLER__
#define __ANALYZEHANDLER__

#include "../FleetAction.h"
#include "../../../config/ConfigHandler.h"

/**
* Handles Analyze....
* You can analyze an entity to find out the resources
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace analyze
{
	class AnalyzeHandler	: public FleetAction
	{
	public:
		AnalyzeHandler(mysqlpp::Row fleet)  : FleetAction(fleet) {	}
		void update();

	};
}
#endif
