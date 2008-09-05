#include <iostream>
#include <math.h>
#include <time.h>
#include <mysql++/mysql++.h>

#include "DebrisHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace debris
{
	void DebrisHandler::update()
	{
	
		/**
		* Fleet-Action: Create debris field
		*
		* Whole fleet will be destroyed!
		*/ 
		
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		
		this->action = std::string(fleet_["action"]);
		
		std::string coordsTarget = functions::formatCoords(fleet_["entity_to"],0);
		std::string coordsFrom = functions::formatCoords(fleet_["entity_from"],0);
		
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
				/** Create a debris field with the fleet (every single ship 40% of the costs) **/
				query << "SELECT ";
				query << "	s.ship_id, ";
				query << "	s.ship_costs_metal, ";
				query << "	s.ship_costs_crystal, ";
				query << "	s.ship_costs_plastic, ";
				query << "	fs.fs_ship_cnt ";
				query << "FROM ";
				query << "	fleet_ships AS fs ";
				query << "	INNER JOIN ships AS s ON fs.fs_ship_id = s.ship_id ";
				query << "	AND fs.fs_fleet_id='" << fleet_["id"] << "';";
				mysqlpp::Result rRes = query.store();
				query.reset();
				
				if (rRes) {
					int rSize = rRes.size();
					mysqlpp::Row rRow;
					
					if (rSize > 0) {

				   		for (mysqlpp::Row::size_type i = 0; i<rSize; i++) {
							rRow = rRes.at(i);

							this->shipCnt = ceil((double)rRow["fs_ship_cnt"]*0.4);
							this->tfMetal += shipCnt * (double)rRow["ship_costs_metal"];
							this->tfCrystal += shipCnt * (double)rRow["ship_costs_crystal"];
							this->tfPlastic += shipCnt * (double)rRow["ship_costs_plastic"];

							query << "DELETE FROM ";
							query << "	fleet_ships ";
							query << "WHERE ";
							query << "	fs_fleet_id='" << fleet_["id"] << "' ";
							query << "	AND fs_ship_id='" << rRow["ship_id"] << "';";
							query.store();
							query.reset();
						}

						/** Save the created field **/
						query << "UPDATE ";
						query << "	planets ";
						query << "SET ";
						query << "	planet_wf_metal=planet_wf_metal+'" << this->tfMetal << "', ";
						query << "	planet_wf_crystal=planet_wf_crystal+'" << this->tfCrystal << "', ";
						query << "	planet_wf_plastic=planet_wf_plastic+'" << this->tfPlastic << "' ";
						query << "WHERE ";
						query << "	id='" << fleet_["entity_to"] << "';";
						query.store();
						query.reset();

						/** Delete the fleet in the db **/
						fleetDelete();

						/** Send a message to the fleet user **/
						std::string msg = "Eine Flotte vom Planeten ";
						msg += coordsFrom;
						msg += " hat auf dem Planeten ";
						msg += coordsTarget;
						msg += " ein Trümmerfeld erstellt.";
						
						functions::sendMsg(fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Trümmerfeld erstellt",msg);

						/** Add a log **/
						std::string log = "Eine Flotte des Spielers [B]";
						log += functions::getUserNick((int)fleet_["user_id"]);
						log += "[/B] vom Planeten ";
						log += coordsFrom;
						log += " hat auf dem Planeten ";
						log += coordsTarget;
						log += " ein Trümmerfeld erstellt.";
						
						functions::addLog(13,log,time);
					}
				}
			}
			
			/** If no ship with the action was in the fleet **/
			else {
				std::string text = "Eine Flotte vom Planeten ";
				text += coordsFrom;
				text += " versuchte, ein Trümmerfel zu erstellen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Trümmer erstellen gescheitert",text);
				
				fleetReturn(1);
			}
		}	
	}
}
