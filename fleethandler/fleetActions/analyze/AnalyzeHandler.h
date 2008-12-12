
#ifndef __ANALYZEHANDLER__
#define __ANALYZEHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

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
		
	private:
		/**
		* Destroypossibility and percentage
		**/
		double shipDestroy, destroy;

		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;

	};
}
#endif
