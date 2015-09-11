
#ifndef __ASTEROIDHANDLER__
#define __ASTEROIDHANDLER__

#include <ctime>
#include <math.h>

#include "FleetAction.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Asteroid....
* Collecting resources from an asteroidfield
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace asteroid
{
	class AsteroidHandler	: public FleetAction
	{
	public:
		AsteroidHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
		
	private:
		/**
		*Calculated/collected resources
		**/
		double metal, crystal, plastic;
		double sum;
		
		/**
		* Possibilitys, if the action succed
		**/
		int one, two;
	};

}
#endif
