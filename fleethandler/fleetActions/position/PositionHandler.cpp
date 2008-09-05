#include <iostream>

#include <mysql++/mysql++.h>

#include "PositionHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace position
{
	void PositionHandler::update()
	{
	
		/**
		* Fleet-Action: Position
		*/
		
		Config &config = Config::instance();
		
		/** Preckeck, if planet user is the same as the fleet user **/
		this->pId =	functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		
		if (this->pId == (int)fleet_["user_id"]) {
			/** Land the fleet and delete it in the db **/
			fleetLand(1);
			fleetDelete();

			/** Send a message to the user **/
			std::string msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ";
			msg += functions::formatCoords((int)fleet_["entity_to"],0);
			msg += "\n[b]Startplanet:[/b] ";
			msg += functions::formatCoords((int)fleet_["entity_from"],0);
			msg += "\n[b]Zeit:[/b] ";
			msg += functions::formatTime((int)fleet_["landtime"]);
			msg += "\n[b]Auftrag:[/b] ";
			msg += functions::fa(std::string(fleet_["action"]));
			msg += msgAllShips;
			msg += msgRes;
		
			functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Flotte angekommen",msg);
		}
		
		/** If the fleet user is not the same as the planet user **/
		else {
			/** Send the fleet to the main planet of the fleet user **/
			fleetSendMain();
			
			/** Send a message to the fleet user **/
			std::string msg = "[b]FLOTTE Landen GESCHEITERT[/b]\n\nEine eurer Flotten hat versucht auf ihrem Ziel zu laden Der Versuch scheiterte jedoch und die Flotte macht sich auf den Weg zu eurem Hauptplaneten!\n\n[b]Ziel:[/b] ";
			msg += functions::formatCoords(fleet_["entity_to"],0);
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
