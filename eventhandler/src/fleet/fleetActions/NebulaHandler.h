
#ifndef __NEBULAHANDLER__
#define __NEBULAHANDLER__

#include <ctime>
#include <math.h>

#include "FleetAction.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Nebula....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace nebula
{
	class NebulaHandler	: public FleetAction
	{
	public:
		NebulaHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
		
	private:
		/**
		*Calculated/collected resources
		**/
		double nebula;
		double sum;
		
		/**
		* Possibilitys, if the action succed
		**/
		int one, two;
	};

}
#endif
