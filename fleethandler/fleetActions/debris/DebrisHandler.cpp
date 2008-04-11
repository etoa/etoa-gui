#include <iostream>
#include <math.h>

#include <time.h>
#include <mysql++/mysql++.h>

#include "DebrisHandler.h"
#include "../../MysqlHandler.H"
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
		
		// Precheck action==possible?
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	ship_id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ON fs_ship_id = ship_id ";
		query << "	AND fs_fleet_id='" << fleet_["fleet_id"] << "' ";
		query << "	AND fs_ship_faked='0' ";
		query << "	AND ship_tf='1';";
		mysqlpp::Result fsRes = query.store();
		query.reset();
		
					
		if (fsRes)
		{
			int fsSize = fsRes.size();
			
			if (fsSize > 0)
			{

				//Verwandelt die ganze Flotte in ein TF (Grösse = 40% der Baukosten)
				query << "SELECT ";
				query << "	s.ship_id, ";
				query << "	s.ship_costs_metal, ";
				query << "	s.ship_costs_crystal, ";
				query << "	s.ship_costs_plastic, ";
				query << "	fs.fs_ship_cnt ";
				query << "FROM ";
				query << "	fleet_ships AS fs ";
				query << "	INNER JOIN ships AS s ON fs.fs_ship_id = s.ship_id ";
				query << "	AND fs.fs_fleet_id='" << fleet_["fleet_id"] << "';";
				mysqlpp::Result rRes = query.store();
				query.reset();
				
				if (rRes)
				{
					int rSize = rRes.size();
					mysqlpp::Row rRow;
					
					if (rSize > 0)
					{
						int cnt;
						double tfMetal, tfCrystal, tfPlastic;
				   		for (mysqlpp::Row::size_type i = 0; i<rSize; i++) 
						{
							rRow = rRes.at(i);

							cnt = ceil((int)rRow["fs_ship_cnt"]*0.4);
							tfMetal += cnt * (double)rRow["ship_costs_metal"];
							tfCrystal += cnt * (double)rRow["ship_costs_crystal"];
							tfPlastic += cnt * (double)rRow["ship_costs_plastic"];

							query << "DELETE FROM ";
							query << "	fleet_ships ";
							query << "WHERE ";
							query << "	fs_fleet_id='" << fleet_["fleet_id"] << "' ";
							query << "	AND fs_ship_id='" << rRow["ship_id"] << "';";
							query.store();
							query.reset();
						}

						//Speichert enstandenes TF (Rohstoffe werden zum bestehenden TF summiert)
						query << "UPDATE ";
						query << "	planets ";
						query << "SET ";
						query << "	planet_wf_metal=planet_wf_metal+'" << tfMetal << "', ";
						query << "	planet_wf_crystal=planet_wf_crystal+'" << tfCrystal << "', ";
						query << "	planet_wf_plastic=planet_wf_plastic+'" << tfPlastic << "' ";
						query << "WHERE ";
						query << "	id='" << fleet_["fleet_entity_to"] << "';";
						query.store();
						query.reset();

						// Flotte-Schiffe-Verknüpfungen löschen
						fleetDelete();

						//Nachricht senden
						std::string coordsTarget = functions::formatCoords((int)fleet_["fleet_entity_to"],0);
						std::string coordsFrom = functions::formatCoords((int)fleet_["fleet_entity_from"],0);
						std::string msg = "Eine Flotte vom Planeten ";
						msg += coordsFrom;
						msg += " hat auf dem Planeten ";
						msg += coordsTarget;
						msg += " ein Trümmerfeld erstellt.";
						
						functions::sendMsg(fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Trümmerfeld erstellt",msg);

						//Log schreiben
						std::string log = "Eine Flotte des Spielers [B]";
						log += functions::getUserNick((int)fleet_["fleet_user_id"]);
						log += "[/B] vom Planeten ";
						log += coordsFrom;
						log += " hat auf dem Planeten ";
						log += coordsTarget;
						log += " ein Trümmerfeld erstellt.";
						
						functions::addLog(13,log,time);
					}
				}
			}
			else
			{
				std::string text = "Eine Flotte vom Planeten ";
				text += functions::formatCoords((int)fleet_["fleet_entity_from"],0);
				text += " versuchte, ein Trümmerfel zu erstellen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Trümmer erstellen gescheitert",text);
				
				fleetReturn("zr");
			}
		}	
	}
}

