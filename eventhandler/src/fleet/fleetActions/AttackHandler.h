
#ifndef __ATTACKHANDLER__
#define __ATTACKHANDLER__

#include "FleetAction.h"
#include "../battle/BattleHandler.h"

/**
* Handles Attack....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace attack
{
	class AttackHandler	: public FleetAction
	{
	public:
		AttackHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();

	};
}
#endif
