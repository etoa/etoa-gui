#include <iostream>
#include <mysql++/mysql++.h>

#include "DeliveryHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace delivery
{
	void DeliveryHandler::update()
	{
	
		/**
		* Fleet-Action: Market-delivery
		*/
		Config &config = Config::instance();

		/** Precheck, watch if the buyer is the same as the planet user **/
		this->planetUserID = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		
		if (this->planetUserID == (int)fleet_["user_id"]) {
			/** Deliver ships **/
			fleetLand(1);

			/** Send a message to the suer **/
			std::string msg = "Eine Flotte von der Allianzbasis hat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
			msg += functions::formatCoords((int)fleet_["entity_to"],0);
			msg += "\n[b]Zeit:[/b] ";
			msg += functions::formatTime((int)fleet_["landtime"]);
			msg += "\n[b]Bericht:[/b] Die erstellten Schiffe sind gelandet.\n";
			msg += msgAllShips;
			
			functions::sendMsg(planetUserID,(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Flotte von der Allianzbasis",msg);

			/** Delete the fleet data **/
			fleetDelete();
		}
		
		/** If the planet user is not the same as the buyer, send fleet to the main and send a message with the info **/
		else {
			fleetSendMain((int)fleet_["user_id"]);
			
			std::string msg = "[b]FLOTTE LANDEN GESCHEITERT[/b]\n\nEine eurer Flotten hat versucht auf ihrem Ziel zu laden Der Versuch scheiterte jedoch und die Flotte macht sich auf den Weg zu eurem Hauptplaneten!\n\n[b]Ziel:[/b] ";
			msg += functions::formatCoords((int)fleet_["entity_to"],0);
			msg += "\n[b]Start:[/b] ";
			msg += functions::formatCoords(fleet_["entity_from"],0);
			msg += "\n[b]Zeit:[/b] ";
			msg += functions::formatTime((int)fleet_["landtime"]);
			msg += "\n[b]Auftrag:[/b] ";
			msg += functions::fa(std::string(fleet_["action"]));
			
			functions::sendMsg((int)fleet_["user_id"],5,"Flotte umgelenkt",msg);
		}
	}
}
