
#ifndef __CANCELHANDLER__
#define __CANCELHANDLER__

#include "../FleetAction.h"

/**
* Handles Cancel....
* For every action, that failed, was stopped or canceled
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace cancel
{
	class CancelHandler	: public FleetAction
	{
	public:
		CancelHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
	};
}
#endif
