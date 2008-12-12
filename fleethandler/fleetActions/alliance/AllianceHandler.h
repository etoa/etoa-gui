
#ifndef __ALLIANCEHANDLER__
#define __ALLIANCEHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"
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
