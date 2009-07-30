
#ifndef __DEFAULTHANDLER__
#define __DEFAULTHANDLER_

#include "FleetAction.h"

/**
* Handles Default....
* Actions they were not declared
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace defaul
{
	class DefaultHandler	: public FleetAction
	{
	public:
		DefaultHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	};
}
#endif
