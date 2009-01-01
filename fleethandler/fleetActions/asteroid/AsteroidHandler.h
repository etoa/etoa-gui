
#ifndef __ASTEROIDHANDLER__
#define __ASTEROIDHANDLER__

#include <ctime>
#include <math.h>

#include "../../FleetHandler.h"
#include "../../config/ConfigHandler.h"

/**
* Handles Asteroid....
* Collecting resources from an asteroidfield
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace asteroid
{
	class AsteroidHandler	: public FleetHandler
	{
	public:
		AsteroidHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	private:
		/**
		*Calculated/collected resources
		**/
		double asteroid;
		double sum;
		
		/**
		* Possibilitys, if the action succed
		**/
		int one, two;
	};

}
#endif
