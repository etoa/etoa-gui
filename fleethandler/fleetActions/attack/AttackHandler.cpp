#include <iostream>

#include <mysql++/mysql++.h>

#include "AttackHandler.h"
#include "../../battle/BattleHandler.h"

namespace attack
{
	void AttackHandler::update()
	{
		/**
		* Fleet-Action: Attack
		*/
		BattleHandler *bh = new BattleHandler(con_, fleet_);
		bh->battle();
		
		// if fleet user has won the fight, send fleet home
		if (bh->returnFleet)
		{
			fleetReturn(1,0,0,0,0,0,0);
		}

		//delete bh;
	}
}

