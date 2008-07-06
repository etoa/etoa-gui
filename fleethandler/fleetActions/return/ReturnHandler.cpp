#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "ReturnHandler.h"
#include "../../MysqlHandler.h"
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
		if (std::string(fleet_["action"])=="sr" || std::string(fleet_["action"])=="tr")
		{
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	user_fleet_rtn_msg ";
			query << "FROM ";
			query << "	users ";
			query << "WHERE ";
			query << "	user_id=" << fleet_["user_id"] << ";";
			mysqlpp::Result mRes = query.store();
			query.reset();
			
			if (mRes)
			{
				int mSize = mRes.size();
				
				if (mSize > 0)
				{
					mysqlpp::Row mRow = mRes.at(0);
					
					if (mRow["user_fleet_rtn_msg"]!="0")
					{
						sendMsg = false;
					}
				}
			}
		}
		
		if (sendMsg)
		{
			//Nachricht senden
			std::string msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ";
			msg += functions::formatCoords((int)fleet_["entity_to"],0);
			msg += "\n[b]Startplanet:[/b] ";
			msg += functions::formatCoords(fleet_["entity_from"],0);
			msg += "\n[b]Zeit:[/b] ";
			msg += functions::formatTime((int)fleet_["landtime"]);
			msg += "\n[b]Auftrag:[/b] ";
			msg += functions::fa(std::string(fleet_["action"]));
				
			msg += msgAllShips;
			msg += msgRes;
			
			functions::sendMsg((int)fleet_["user_id"],5,"Flotte angekommen",msg);
		}
	}
}
