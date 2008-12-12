#include <iostream>

#include <mysql++/mysql++.h>

#include "StealthHandler.h"
#include "../../battle/BattleHandler.h"

namespace stealth
{
	void StealthHandler::update()
	{
	
		/**
		* Fleet-Action: Stealth Attack
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
