#include <iostream>

#include <mysql++/mysql++.h>

#include "ColonializeHandler.h"
#include "../../MysqlHandler.h"
#include "../../config/ConfigHandler.h"
#include "../../functions/Functions.h"

namespace colonialize
{
	void ColonializeHandler::update()
	{
	
		/**
		* Fleet-Action: Colonialize
		*/
		Config &config = Config::instance();
		
		this->action = std::string(fleet_["action"]);;

		std::string coordsFrom = functions::formatCoords(fleet_["entity_from"],0);
		std::string coordsTarget = functions::formatCoords(fleet_["entity_to"],0);
		
		// Precheck action==possible?
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	ship_id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ON fs_ship_id = ship_id ";
		query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
		query << "	AND fs_ship_faked='0' ";
		query << "	AND ship_actions LIKE '%" << this->action << "%';";
		mysqlpp::Result fsRes = query.store();
		query.reset();
					
		if (fsRes) {
			int fsSize = fsRes.size();
			
			if (fsSize > 0) {
				/** Check if the planet has alreasy an user **/
				query << "SELECT  ";
				query << "	planet_user_id ";
				query << "FROM ";
				query << "	planets ";
				query << "WHERE ";
				query << "	planet_user_id>0 ";
				query << "	AND id='" << fleet_["entity_to"] << "';";
				mysqlpp::Result uRes = query.store();
				query.reset();
		
				if (uRes) {
					int uSize = uRes.size();
			
					if (uSize > 0) {
						mysqlpp::Row uRow = uRes.at(0);
				
						/** If the planet user ist the same as the fleet user, land the fleet **/
						if((int)uRow["planet_user_id"] == (int)fleet_["user_id"]) {
							fleetLand(1,1);
							fleetDelete();

							/** Send a message to the user **/
							std::string msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
							msg += coordsTarget;
							msg += "\n[b]Zeit:[/b] ";
							msg += functions::formatTime((int)fleet_["landtime"]);
							msg += "\n[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!";
							msg += msgAllShips;
							msg += msgRes;
							
							functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Flotte angekommen",msg);
						}
	  
						/** If the planet belongs to en other user, return the fleet back home **/
						else {
							/** Send a message to the user **/
							std::string msg = "Die Flotte kann den Planeten nicht kolonialisieren, da er bereits von einem anderen Volk kolonialisiert wurde!\n";
					
							functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Landung nicht möglich",msg);

							/** Send the fleet back home again **/
							fleetReturn(2);
						}
					}
			
					/** if the planet has not yet a user **/
					else {
						/** Check if the user has already its planet maximum **/
						query << "SELECT ";
						query << "	COUNT(planet_user_id) AS cnt ";
						query << "FROM ";
						query << "	planets ";
						query << "WHERE ";
						query << "	planet_user_id='" << fleet_["user_id"] << "';";
						mysqlpp::Result uRes = query.store();
						query.reset();
				
						if (uRes) {
							int uSize = uRes.size();
					
							if (uSize > 0) {
								mysqlpp::Row uRow = uRes.at(0);
		
								/** User has already the maximum **/
								if ((int)uRow["cnt"] >= config.nget("user_max_planets",0)) {
									/** Send a message to the user **/
									std::string msg = "Die Flotte kann den Planeten nicht kolonialisieren, da die maximale Zahl an Planeten auf denen du regieren darfst, bereits erreicht worden ist!\n";
							
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Landung nicht möglich",msg);
	
									/** Send fleet home again **/
									fleetReturn(2);
								}
    
								/** if up to now everything is fine, let's colonialize the planet **/
								else {
									/** reset the planet **/
									functions::resetPlanet((int)fleet_["entity_to"]);

									/** Take it **/
									query << "UPDATE ";
									query << "	planets ";
									query << "SET ";
									query << "	planet_user_id='" << fleet_["user_id"] << "', ";
									query << "	planet_name='Unbenannt' ";
									query << "WHERE ";
									query << "	id='" << fleet_["entity_to"] << "';";
									query.store();
									query.reset();

									/** Land the fleet and delete one ship (action colonialize **/
									fleetLand(1);

									/** Delete the fleet from the db **/
									fleetDelete();

									/** Send a message to the user **/
									std::string msg = "Die Flottehat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
									msg += coordsTarget;
									msg += "\n[b]Zeit:[/b] ",
									msg	+= functions::formatTime((int)fleet_["landtime"]);
									msg += "\n";
									msg += "[b]Bericht:[/b] Die Flotte hat eine neue Kolonie errichtet! Dabei wurde ein Besiedlungsschiff verbraucht.\n";
									msg += msgAllShips;
									msg += msgRes;
						
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Planet kolonialisiert",msg);
								}
							}
						}
					}
				}
			}
			/** If there isnt any asteroid colecter in the fleet **/
			else {
				std::string text = "Eine Flotte vom Planeten ";
				text += coordsFrom;
				text += " versuchte, eine Kolonie zu errichten. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Kolonisieren gescheitert",text);
				
				fleetReturn(1);
			}
		}
	}
}
