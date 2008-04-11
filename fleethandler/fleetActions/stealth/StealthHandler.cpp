#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "StealthHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"

namespace stealth
{
	void StealthHandler::update()
	{
	
		/**
		* Fleet-Action: Stealth Attack
		*/

		// Calc battle
		battle();

		// Send messages
		// Send messages
		int userToId = functions::getUserIdByPlanet((int)fleet_["fleet_target_to"]);
		std::string subject1 = "Kampfbericht (";
		subject1 += bstat;
		subject1 += ")";
		std::string = subject2 = "Kampfbericht (";
		subject2 += bstat2;
		subject2 += ")";
		functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,subject1,msgFight);
		functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,subject2,msgFight);

		// Add log
		functions::addLog(1,msgFight,(int)fleet_["fleet_landtime"]);

		// Flotte zurückschicken
		if (returnFleet)
		{
			action = "vr";
			fleetReturn(action);
		}
	}
}
