
#ifndef __DEFAULTHANDLER__
#define __DEFAULTHANDLER_

#include "../../FleetHandler.h"

/**
* Handles Default....
* Actions they were not declared
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace defaul
{
	class DefaultHandler	: public FleetHandler
	{
	public:
		DefaultHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};
}
#endif
