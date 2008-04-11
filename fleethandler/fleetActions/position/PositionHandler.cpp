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
            
		//Flotte stationieren und Waren ausladen
		fleetLand(1);

		// Flotte-Schiffe-Verknüpfungen löschen
		fleetDelete();

		//Nachricht senden
		std::string msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ";
		msg += functions::formatCoords((int)fleet_["fleet_entity_to"],0);
		msg += "\n[b]Startplanet:[/b] ";
		msg += functions::formatCoords((int)fleet_["fleet_entity_from"],0);
		msg += "\n[b]Zeit:[/b] ";
		msg += functions::formatTime((int)fleet_["fleet_landtime"]);
		msg += "\n[b]Auftrag:[/b] ";
		msg += functions::fa(std::string(fleet_["fleet_action"]));
		msg += msgAllShips;
		msg += msgRes;
		
		functions::sendMsg((int)fleet_["fleet_user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Flotte angekommen",msg);
	}
}
