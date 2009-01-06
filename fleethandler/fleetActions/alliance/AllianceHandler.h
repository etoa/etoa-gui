
#ifndef __ALLIANCEHANDLER__
#define __ALLIANCEHANDLER__

#include "../../FleetHandler.h"
#include "../../battle/BattleHandler.h"

/**
* Handles AlliancaAttack....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace alliance
{
	class AllianceHandler	: public FleetHandler
	{
	public:
		AllianceHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	};
}
#endif
