#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "ReturnHandler.h"
#include "../../MysqlHandler.H"
#include "../../functions/Functions.h"

namespace retour
{
	void ReturnHandler::update()
	{
	
		/**
		* Fleet-Action: Returned flight
		*/
            
		//Flotte stationieren und Waren ausladen
		fleetLand(1);

		// Flotte-Schiffe-VerknÃ¼pfungen lÃ¶schen
		fleetDelete();

		sendMsg = true;
		
		// Für Transporte und Spionage prüfen ob Return Nachricht gewünscht ist
		if (std::string(fleet_["fleet_action"])=="sr" || std::string(fleet_["fleet_action"])=="tr")
		{
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	user_fleet_rtn_msg ";
			query << "FROM ";
			query << "	users ";
			query << "WHERE ";
			query << "	user_id=" << fleet_["fleet_user_id"] << ";";
			mysqlpp::Result mRes = query.store();
			query.reset();
			
			if (mRes)
			{
				int mSize = mRes.size();
				
				if (mSize > 0)
				{
					mysqlpp::Row mRow = mRes.at(0);
					
					if (mRow["usere_fleet_rtn_msg"]!="0")
					{
						sendMsg = false;
					}
				}
			}
		}
		
		if (sendMsg)
		{
			//Nachricht senden
			std::string msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat ihr Ziel erreicht!\n[b]Zielplanet:[/b] ";
			msg += functions::formatCoords((int)fleet_["fleet_target_to"]);
			msg += "[b]Startplanet:[/b] ";
			msg += functions::formatCoords(fleet_["fleet_target_from"]);
			msg += "[b]Zeit:[/b] ";
			msg += functions::formatTime((int)fleet_["fleet_landtime"]);
			msg += "[b]Auftrag:[/b] ";
			msg += functions::fa(std::string(fleet_["fleet_action"]));
				
			msg += msgAllShips;
			msg += msgRes;
			
			functions::sendMsg((int)fleet_["fleet_user_id"],5,"Flotte angekommen",msg);
		}
	}
}

