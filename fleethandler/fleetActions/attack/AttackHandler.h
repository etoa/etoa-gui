
#ifndef __ATTACKHANDLER__
#define __ATTACKHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"
#include "../../battle/BattleHandler.h"

/**
* Handles Attack....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace attack
{
	class AttackHandler	: FleetHandler
	{
	public:
		AttackHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	};
}
#endif
