#include <iostream>
#include <vector>
#include <algorithm>
#include <math.h>

#include <mysql++/mysql++.h>

#include "FetchHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace fetch
{
	void FetchHandler::update()
	{
	
		/**
		* Fleet-Action: Fetch
		*/
		Config &config = Config::instance();
		std::string action = "fetch";

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
		query << "	AND (";
		query << "		ship_actions LIKE '%," << action << "'";
		query << "		OR ship_actions LIKE '" << action << ",%'";
		query << "		OR ship_actions LIKE '%," << action << ",%'";
		query << "		OR ship_actions LIKE '" << action << "');";
		mysqlpp::Result fsRes = query.store();
		query.reset();
							
		if (fsRes)
		{
			int fsSize = fsRes.size();
			
			if (fsSize > 0)
			{
		
				mysqlpp::Query query = con_->query();
				query << "SELECT ";
				query << "	planet_res_metal, ";
				query << "	planet_res_crystal, ";
				query << "	planet_res_plastic, ";
				query << "	planet_res_fuel, ";
				query << "	planet_res_food, ";
				query << "	planet_people, ";
				query << "	planet_user_id ";
				query << "FROM ";
				query << "	planets ";
				query << "WHERE ";
				query << "	id='" << fleet_["entity_to"] << "';";
				mysqlpp::Result pRes = query.store();
				query.reset();
		
				mysqlpp::Row pRow = pRes.at(0);

				if ((int)fleet_["user_id"] == (int)pRow["planet_user_id"])
				{
					double capa = 0;
					query << "SELECT ";
					query << "	SUM(ship_capacity*fs_ship_cnt) as capa ";
					query << "FROM ";
					query << "	fleet_ships ";
					query << "INNER JOIN ";
					query << "	ships ON fs_ship_id = ship_id ";
					query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
					query << "	AND fs_ship_faked='0';";
					mysqlpp::Result capaRes = query.store();
					query.reset();	
					
					if (capaRes)
					{
						int capaSize = capaRes.size();
						
						if (capaSize > 0)
						{
							mysqlpp::Row capaRow = capaRes.at(0);
							int capa = (double)capaRow["capa"] - (double)fleet_["res_metal"] - (double)fleet_["res_crystal"] - (double)fleet_["res_plastic"] - (double)fleet_["res_fuel"] - (double)fleet_["res_food"];
						}
					}
					
					double capaCnt = 0;
			
					std::vector<double> load (5);
			
					load[0]=0;
					load[1]=0;
					load[2]=0;
					load[3]=0;
					load[4]=0;
		
					load[0] = floor(std::min(std::min((double)fleet_["fetch_metal"],(double)pRow["planet_res_metal"]),capa));
					capaCnt += load[0];
					if (capaCnt < capa)
					{
						load[1] = floor(std::min(std::min((double)fleet_["fetch_crystal"],(double)pRow["planet_res_crystal"]),capa-capaCnt));
						capaCnt += load[1];
						if (capaCnt < capa)
						{
							load[2] = floor(std::min(std::min((double)fleet_["fetch_plastic"],(double)pRow["planet_res_plastic"]),capa-capaCnt));
							capaCnt += load[2];
							if (capaCnt < capa)
							{
								load[3] = floor(std::min(std::min((double)fleet_["fetch_fuel"],(double)pRow["planet_res_fuel"]),capa-capaCnt));
								capaCnt += load[3];
								if (capaCnt < capa)
								{
									load[4] = floor(std::min(std::min((double)fleet_["fetch_food"],(double)pRow["planet_res_food"]),capa-capaCnt));
									capaCnt += load[4];
								}
							}			
						}				
					}		
		
					double loadPeople = std::min(std::min((double)fleet_["fetch_people"],(double)fleet_["capacity_people"]),(double)pRow["planet_people"]);
		
					std::string msg = "[B]WAREN ABGEHOLT[/B]\n\nEine Flotte vom Planeten \n[b]";
					msg += functions::formatCoords((int)fleet_["entity_from"],0);
					msg += "[/b]\nhat ihr Ziel erreicht!\n\n[b]Planet:[/b] ";
					msg += functions::formatCoords((int)fleet_["entity_to"],0);
					msg += "\n[b]Zeit:[/b] ";
					msg += functions::formatTime((int)fleet_["landtime"]);
					msg += "\n";
					msg += "\nFolgende Waren wurden abgeholt: \n\n[table]";
					msg += "[tr][th]Titan[/th][td]";
					msg += functions::nf(functions::d2s(load[0]));
					msg += "[/td][/tr]";
					msg += "[tr][th]Silizium[/th][td]";
					msg += functions::nf(functions::d2s(load[1]));
					msg += "[/td][/tr]";
					msg += "[tr][th]PVC[/th][td]";
					msg += functions::nf(functions::d2s(load[2]));
					msg += "[/td][/tr]";
					msg += "[tr][th]Tritium[/th][td]";
					msg += functions::nf(functions::d2s(load[3]));
					msg += "[/td][/tr]";
					msg += "[tr][th]Nahrung[/th][td]";
					msg += functions::nf(functions::d2s(load[4]));
					msg += "[/td][/tr]";
			
					if (loadPeople>0)
					{
						msg += "[tr][th]Bewohner[/th][td]";
						msg += functions::nf(functions::d2s(loadPeople));
						msg += "[/td][/tr]";
					}
					msg += "[/table]";
					
					
					query << "UPDATE ";
					query << "	planets ";
					query << "SET ";
					query << "	planet_res_metal=planet_res_metal-'" << load[0] << "', ";
					query << "	planet_res_crystal=planet_res_crystal-'" << load[1] << "', ";
					query << "	planet_res_plastic=planet_res_plastic-'" << load[2] << "', ";
					query << "	planet_res_fuel=planet_res_fuel-'" << load[3] << "', ";
					query << "	planet_res_food=planet_res_food-'" << load[4] << "', ";
					query << "	planet_people=planet_people-'" << loadPeople << "' ";
					query << "WHERE ";
					query << "	id='" << fleet_["entity_to"] << "';";
					query.store();
					query.reset();		
		
					// Nachrichten senden
					functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Warenabholung",msg);
					fleetReturn(1,load[0],load[1],load[2],load[3],load[4],loadPeople);
				}
				else
				{
					fleetReturn(1);
				}
			}
			else
			{
				fleetReturn(1);
			}
		}
		else
		{
			fleetReturn(1);
		}
	}
}
