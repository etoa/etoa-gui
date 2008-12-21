#include <iostream>

#include <mysql++/mysql++.h>

#include "AllianceHandler.h"
#include "../../battle/BattleHandler.h"

namespace alliance
{
	void AllianceHandler::update()
	{
		/**
		* Fleet-Action: Attack
		*/
		BattleHandler *bh = new BattleHandler(con_, fleet_);
		bh->battle();
		
		// if fleet user has won the fight, send fleet home
		if (bh->returnFleet)
		{
			fleetReturn(1);
		}

		//delete bh;
	}
}

