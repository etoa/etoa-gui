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

		/** Check if the planet User is the same as the fleet user **/
		this->planetUserId =	functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		
		if (this->planetUserId == (int)fleet_["user_id"]) {
			/** Land fleet and delete entries in the database **/
			fleetLand(1,1,1);
			fleetDelete();
			
			/** Check if the user'd like to have a return message for spy and transport **/
			this->sendMsg = true;
		
			if (std::string(fleet_["action"])=="spy" || std::string(fleet_["action"])=="transport") {
				mysqlpp::Query query = con_->query();
				query << "SELECT ";
				query << "	fleet_rtn_msg ";
				query << "FROM ";
				query << "	user_properties ";
				query << "WHERE ";
				query << "	id=" << fleet_["user_id"] << ";";
				mysqlpp::Result mRes = query.store();
				query.reset();
			
				if (mRes) {
					int mSize = mRes.size();
				
					if (mSize > 0) {
						mysqlpp::Row mRow = mRes.at(0);
					
						if (mRow["fleet_rtn_msg"]!="0") {
							this->sendMsg = false;
						}
					}
				}
			}
			
			/** If the check is ok, send a message to the user **/
			if (this->sendMsg) {
				std::string msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat ihr Ziel erreicht!\n\n[b]Ziel:[/b] ";
				msg += functions::formatCoords((int)fleet_["entity_to"],0);
				msg += "\n[b]Start:[/b] ";
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
		
		/** If the planet user is not the same as the fleet user, send fleet to the main and send a message with the info **/
		else {
			fleetSendMain();
			
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
