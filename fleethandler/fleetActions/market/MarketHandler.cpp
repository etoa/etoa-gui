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
		
		if (sRes) {
			int sSize = sRes.size();
			
			if (sSize > 0) {
				mysqlpp::Row sRow = sRes.at(0);

				if ((int)sRow["fs_ship_id"]==config.idget("MARKET_SHIP_ID") && sSize==1) {
					this->landAction = 2;
				}
			}
		}

		/** Precheck, watch if the buyer is the same as the planet user **/
		this->planetUserID = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		
		if (this->planetUserID == (int)fleet_["next_id"]) {
			/** Deliver ships and resources **/
			if (this->landAction==1) {
				/** Land fleet and save the resources and the ships on the planet **/
				fleetLand(1);

				/** Send a message to the suer **/
				std::string msg = "Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
				msg += functions::formatCoords((int)fleet_["entity_to"],0);
				msg += "\n[b]Zeit:[/b] ";
				msg += functions::formatTime((int)fleet_["landtime"]);
				msg += "\n[b]Bericht:[/b] Die gekauften Schiffe sind gelandet.\n";
				msg += msgAllShips;
			
				/** If the ship deliver resources add the resource part of the message **/
				if((int)fleet_["res_metal"]!='0' || (int)fleet_["res_crystal"]!='0' || (int)fleet_["res_plastic"]!='0' || (int)fleet_["res_fuel"]!='0' || (int)fleet_["res_food"]!='0') {
					msg += "\nEs wurden zudem folgende Rohstoffe abgeladen:\n";
					msg += msgRes;
				}

				msg += "\n\nUnser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium";
				functions::sendMsg(planetUserID,(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Flotte vom Handelsministerium",msg);
			}
	
			/** If there were only resources delivered **/
			else {
				/** Save them on the planet **/
				fleetLand(2);

				/** Send a message to the planet user **/
				std::string msg = "Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
				msg += functions::formatCoords((int)fleet_["entity_to"],0);
				msg += "\n[b]Zeit:[/b] ";
				msg += functions::formatTime((int)fleet_["landtime"]);
				msg += "\n[b]Bericht:[/b] Folgende Waren wurden ausgeladen:\n";
				msg += msgRes;
				msg += "\n\nUnser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium";
			
				functions::sendMsg(planetUserID,(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Transport vom Handelsministerium",msg);
			}

			/** Delete the fleet data **/
			fleetDelete();
		}
		
		/** If the planet user is not the same as the buyer, send fleet to the main and send a message with the info **/
		else {
			fleetSendMain((int)fleet_["next_id"]);
			
			std::string msg = "[b]FLOTTE LANDEN GESCHEITERT[/b]\n\nEine eurer Flotten hat versucht auf ihrem Ziel zu laden Der Versuch scheiterte jedoch und die Flotte macht sich auf den Weg zu eurem Hauptplaneten!\n\n[b]Ziel:[/b] ";
			msg += functions::formatCoords((int)fleet_["entity_to"],0);
			msg += "\n[b]Start:[/b] ";
			msg += functions::formatCoords(fleet_["entity_from"],0);
			msg += "\n[b]Zeit:[/b] ";
			msg += functions::formatTime((int)fleet_["landtime"]);
			msg += "\n[b]Auftrag:[/b] ";
			msg += functions::fa(std::string(fleet_["action"]));
			
			functions::sendMsg((int)fleet_["next_id"],5,"Flotte umgelenkt",msg);
		}
	}
}
