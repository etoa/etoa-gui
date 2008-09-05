#include <iostream>
#include <vector>
#include <time.h>
#include <mysql++/mysql++.h>

#include "CancelHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"

namespace cancel
{
	void CancelHandler::update()
	{
		/**
		* Fleet-Action: Cancelled flight
		*/

		/** Check it fleet user is the same as the planet user **/
		this->planetUserId =	functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		
		if (this->planetUserId == (int)fleet_["user_id"]) {
			/** Land the fleet and delete it in the database **/
			fleetLand(1);

			fleetDelete();
	
			/** Send a message to the user **/
			std::string msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ";
			msg += functions::formatCoords((int)fleet_["entity_to"],0);
			msg += "\n[b]Startplanet:[/b] ";
			msg += functions::formatCoords((int)fleet_["entity_from"],0);
			msg += "\n[b]Zeit:[/b] ";
			msg += functions::formatTime((int)fleet_["landtime"]);
			msg += "\n[b]Auftrag:[/b] ";
			msg += functions::fa(std::string(fleet_["action"]));
			msg += msgAllShips;
			msg += msgRes;
			msg += "";
			functions::sendMsg((int)fleet_["user_id"],5,"Flotte angekommen",msg);
		}
		
		/** If the fleet user isnt the same as the planet user **/
		else {
			/** Send the fleet to user's mainplanet **/
			fleetSendMain();
			
			/** Send a message to the user **/
			std::string msg = "[b]FLOTTE Landen GESCHEITERT[/b]\n\nEine eurer Flotten hat versucht auf ihrem Ziel zu laden Der Versuch scheiterte jedoch und die Flotte macht sich auf den Weg zu eurem Hauptplaneten!\n\n[b]Ziel:[/b] ";
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
