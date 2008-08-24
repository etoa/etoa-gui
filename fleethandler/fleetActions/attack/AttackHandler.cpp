#include <iostream>

#include <mysql++/mysql++.h>

#include "AttackHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

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

		Config &config = Config::instance();
		
		
		// Send messages
		int userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		std::string subject1 = "Kampfbericht (";
		subject1 += bh->bstat;
		subject1 += ")";
		std::string subject2 = "Kampfbericht (";
		subject2 += bh->bstat2;
		subject2 += ")";
		functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),subject1,bh->msg);
		functions::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),subject2,bh->msg);

		// Add log
		functions::addLog(1,bh->msg,(int)fleet_["landtime"]);

		std::cout << bh->returnFleet << "\n";
		// Flotte zurückschicken
		/*if (bh->returnFleet)
		{
			fleetReturn(1,0,0,0,0,0,0);
		}*/

		//delete bh;
	}
}

