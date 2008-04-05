#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "CancelHandler.h"
#include "../../MysqlHandler.H"
#include "../../functions/Functions.h"

namespace cancel
{
	void CancelHandler::update()
	{
		/**
		* Fleet-Action: Cancelled flight
		*/
            
		//Flotte stationieren und Waren ausladen
		fleetLand(1);

		// Flotte-Schiffe-Verknüpfungen löschen
		fleetDelete();
	
		//Nachricht senden
		std::string msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ";
		msg += functions::formatCoords((int)fleet_["fleet_target_to"]);
		msg += "\n[b]Startplanet:[/b] ";
		msg += functions::formatCoords((int)fleet_["fleet_target_from"]);
		msg += "\n[b]Zeit:[/b] ";
		msg += functions::formatTime((int)fleet_["fleet_landtime"]);
		msg += "\n[b]Auftrag:[/b] ";
		msg += functions::fa(std::string(fleet_["fleet_action"]));
		msg += msgAllShips;
		msg += msgRes;
		msg += "";
		functions::sendMsg((int)fleet_["fleet_user_id"],5,"Flotte angekommen",msg);

	}	
}
