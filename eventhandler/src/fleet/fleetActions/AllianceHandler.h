
#ifndef __ALLIANCEHANDLER__
#define __ALLIANCEHANDLER__

#include "FleetAction.h"
#include "../battle/BattleHandler.h"

/**
* Handles AlliancaAttack....
*
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace alliance
{
	class AllianceHandler	: public FleetAction
	{
	public:
		AllianceHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();

	};
}
#endif
