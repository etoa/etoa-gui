
#ifndef __ATTACKHANDLER__
#define __ATTACKHANDLER__

#include "../../FleetHandler.h"
#include "../../battle/BattleHandler.h"

/**
* Handles Attack....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace attack
{
	class AttackHandler	: public FleetHandler
	{
	public:
		AttackHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	};
}
#endif
