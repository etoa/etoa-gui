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
		query << "	id='" << fleet_["fleet_entity_to"] << "';";
		mysqlpp::Result pRes = query.store();
		query.reset();
		
		mysqlpp::Row pRow = pRes.at(0);

		if ((int)fleet_["fleet_user_id"] == (int)pRow["planet_user_id"])
		{
			double capa = (double)fleet_["fleet_res_metal"] + (double)fleet_["fleet_res_crystal"] + (double)fleet_["fleet_res_plastic"] + (double)fleet_["fleet_res_fuel"] + (double)fleet_["fleet_res_food"] + (double)fleet_["fleet_capacity"];
			double capaCnt = 0;
			
			std::vector<double> load (5);
			
			load[0]=0;
			load[1]=0;
			load[2]=0;
			load[3]=0;
			load[4]=0;
		
			load[0] = floor(std::min(std::min((double)fleet_["fleet_fetch_metal"],(double)pRow["planet_res_metal"]),capa));
			capaCnt += load[0];
			if (capaCnt < capa)
			{
				load[1] = floor(std::min(std::min((double)fleet_["fleet_fetch_crystal"],(double)pRow["planet_res_crystal"]),capa-capaCnt));
				capaCnt += load[1];
				if (capaCnt < capa)
				{
					load[2] = floor(std::min(std::min((double)fleet_["fleet_fetch_plastic"],(double)pRow["planet_res_plastic"]),capa-capaCnt));
					capaCnt += load[2];
					if (capaCnt < capa)
					{
						load[3] = floor(std::min(std::min((double)fleet_["fleet_fetch_fuel"],(double)pRow["planet_res_fuel"]),capa-capaCnt));
						capaCnt += load[3];
						if (capaCnt < capa)
						{
							load[4] = floor(std::min(std::min((double)fleet_["fleet_fetch_food"],(double)pRow["planet_res_food"]),capa-capaCnt));
							capaCnt += load[4];
						}
					}			
				}				
			}		
		
			double loadPeople = std::min(std::min((double)fleet_["fleet_fetch_people"],(double)fleet_["fleet_capacity_people"]),(double)pRow["planet_people"]);
		
			std::string msg = "[B]WAREN ABGEHOLT[/B]\n\nEine Flotte vom Planeten \n[b]";
			msg += functions::formatCoords((int)fleet_["fleet_entity_from"],0);
			msg += "[/b]\nhat ihr Ziel erreicht!\n\n[b]Planet:[/b] ";
			msg += functions::formatCoords((int)fleet_["fleet_entity_to"],0);
			msg += "\n[b]Zeit:[/b] ";
			msg += functions::formatTime((int)fleet_["fleet_landtime"]);
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
			query << "	id='" << fleet_["fleet_entity_to"] << "';";
			query.store();
			query.reset();		
		
			// Nachrichten senden
			functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Warenabholung",msg);
			fleetReturn("fr",load[0],load[1],load[2],load[3],load[4],loadPeople);
		}
		else
		{
			fleetReturn("fr");
		}
	}
}
