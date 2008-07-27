#include <iostream>

#include <mysql++/mysql++.h>

#include "MarketHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace market
{
	void MarketHandler::update()
	{
	
		/**
		* Fleet-Action: Market-delivery
		*/
		Config &config = Config::instance();

		this->landAction = 1;
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	fs_ship_id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "WHERE ";
		query << "	fs_fleet_id=" << fleet_["id"] << ";";
		mysqlpp::Result sRes = query.store();
		query.reset();
		
		if (sRes)
		{
			int sSize = sRes.size();
			
			if (sSize > 0)
			{
				mysqlpp::Row sRow = sRes.at(0);

				if ((int)sRow["fs_ship_id"]==config.idget("MARKET_SHIP_ID") && sSize==1)
				{
					landAction = 2;
				}
			}
		}

		//Sucht User-ID
		this->userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);

		// Resources and ships
		if (this->landAction==1)
		{
			//Flotte stationieren und Waren ausladen
			fleetLand(1);

			//Nachricht senden
			std::string msg = "Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
			msg += functions::formatCoords((int)fleet_["entity_to"],0);
			msg += "\n[b]Zeit:[/b] ";
			msg += functions::formatTime((int)fleet_["landtime"]);
			msg += "\n[b]Bericht:[/b] Die gekauften Schiffe sind gelandet.\n";
			msg += msgAllShips;
			
			//Wenn das schiff auch Rohstoffe mitgebracht hat
			if((int)fleet_["res_metal"]!='0' || (int)fleet_["res_crystal"]!='0' || (int)fleet_["res_plastic"]!='0' || (int)fleet_["res_fuel"]!='0' || (int)fleet_["res_food"]!='0')
			{
				//Nachricht, wie viele Rohstoffe abgeladen wurden
				msg += "Es wurden zudem folgende Rohstoffe abgeladen:\n";
				msg += msgRes;
			}

			msg += "\n\nUnser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium";

			functions::sendMsg(userToId,(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Flotte vom Handelsministerium",msg);
		}
	
		// Only resources
		else
		{
			//Waren ausladen
			fleetLand(2);

			//Nachricht senden
			std::string msg = "Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
			msg += functions::formatCoords((int)fleet_["entity_to"],0);
			msg += "\n[b]Zeit:[/b] ";
			msg += functions::formatTime((int)fleet_["landtime"]);
			msg += "\n[b]Bericht:[/b] Folgende Waren wurden ausgeladen:\n";
			msg += msgRes;
			msg += "\n\nUnser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium";
			
			functions::sendMsg(userToId,(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Transport vom Handelsministerium",msg);
		}

		// Flotte-Schiffe-Verknüpfungen löschen
		fleetDelete();
	}
}
