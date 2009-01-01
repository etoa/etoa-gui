
#ifndef __GASHANDLER__
#define __GASHANDLER__

#include <ctime>
#include <math.h>

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Gas....
* Collect gas from a gas planet
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace gas
{
	class GasHandler	: public FleetHandler
	{
	public:
		GasHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:		
		/**
		*Calculated/collected resources
		**/
		double fuel;
		double sum;
		
		/**
		* Possibilitys, if the action succed
		**/
		int one, two;
	};

}
#endif
