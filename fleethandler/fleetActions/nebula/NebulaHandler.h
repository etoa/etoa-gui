
#ifndef __NEBULAHANDLER__
#define __NEBULAHANDLER__

#include <ctime>
#include <math.h>

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Nebula....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace nebula
{
	class NebulaHandler	: public FleetHandler
	{
	public:
		NebulaHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
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
