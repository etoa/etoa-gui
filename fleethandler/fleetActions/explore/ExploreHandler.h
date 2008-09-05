
#ifndef __AEXPLOREHANDLER__
#define __AEXPLOREHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles explorer....
* Important to discover the universe and to get some quest's
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
		
		/**
		* Actionname (lots of problems with the string variable)
		**/
		std::string action;
		
	private:
		/**
		* Eventaction for quest's 
		*
		* @author Glaubinix
		**/
		std::string event();
		
		/**
		* Coords to calculate the position in the mask
		**/		
		int absX, absY, pos;
		int sxNum, cxNum, syNum, cyNum;
		
		/**
		* Variables to calculate the event
		**/
		int one, two;
		double days;
	};
}
#endif
