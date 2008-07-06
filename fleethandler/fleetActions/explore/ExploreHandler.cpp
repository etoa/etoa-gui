#include <iostream>

#include <mysql++/mysql++.h>

#include "ExploreHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace explore
{
	void ExploreHandler::update()
	{
	
		/**
		* Fleet action: Explore
		*/
		Config &config = Config::instance();
		int userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		std::string action = "explore";

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
			
				query << "SELECT ";
				query << "	code ";
				query << "FROM ";
				query << " entities ";
				query << "WHERE ";
				query << "	id='" << fleet_["entity_to"] << "';";
				mysqlpp::Result codeRes = query.store();
				query.reset();
				
				if (codeRes)
				{
					int codeSize = codeRes.size();
					
					if (codeSize > 0)
					{
						mysqlpp::Row codeRow = codeRes.at(0);
						
						//nebula?
						if (std::string(codeRow["code"])=="n")
						{
							query << "SELECT ";
							query << "	resources ";
							query << "FROM ";
							query << " nebulas ";
							query << "WHERE ";
							query << "	id='" << fleet_["entity_to"] << "';";
							mysqlpp::Result nebulaRes = query.store();
							query.reset();
							
							if (nebulaRes)
							{
								int nebulaSize = nebulaRes.size();
								
								if (nebulaSize > 0)
								{
									mysqlpp::Row nebulaRow = nebulaRes.at(0);
				
									//Nachricht senden
									std::string msg = "Eine Flotte vom Planeten \n[b]";
									msg += functions::formatCoords((int)fleet_["entity_from"],0);
									msg += "[/b]\nhat [b]ein Nebelfeld (";
									msg += functions::formatCoords((int)fleet_["entity_to"],2);
									msg += ")[/b]\num [b]";
									msg += functions::formatTime((int)fleet_["landtime"]);
									msg += "[/b]\n spioniert.\n";
									msg += "\n[b]ROHSTOFFE:[/b]\n\nSilizium: ";
									msg += functions::nf(std::string(nebulaRow["resources"]));
									msg += "\n";
									
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Nebelfeld spionieren",msg);
	
								}
							}
						}
						//asteroid?
						else if (std::string(codeRow["code"])=="a")
						{
							query << "SELECT ";
							query << "	res_metal,res_crystal,res_plastic,res_fuel,res_food ";
							query << "FROM ";
							query << " asteroids ";
							query << "WHERE ";
							query << "	id='" << fleet_["entity_to"] << "';";
							mysqlpp::Result asteroidRes = query.store();
							query.reset();
							
							if (asteroidRes)
							{
								int asteroidSize = asteroidRes.size();
								
								if (asteroidSize > 0)
								{
									mysqlpp::Row asteroidRow = asteroidRes.at(0);
								
									//Nachricht senden
									std::string msg = "Eine Flotte vom Planeten \n[b]";
									msg += functions::formatCoords((int)fleet_["entity_from"],0);
									msg += "[/b]\nhat [b]ein Asteroidenfeld (";
									msg += functions::formatCoords((int)fleet_["entity_to"],2);
									msg += ")[/b]\num [b]";
									msg += functions::formatTime((int)fleet_["landtime"]);
									msg += "[/b]\n spioniert.\n";
									msg += "\nROHSTOFFE:\n\nTitan: ";
									msg += functions::nf(std::string(asteroidRow["res_metal"]));
									msg += "\nSilizium: ";
									msg += functions::nf(std::string(asteroidRow["res_crystal"]));
									msg += "\nPVC: ";
									msg += functions::nf(std::string(asteroidRow["res_plastic"]));
									msg += "\nTritium: ";
									msg += functions::nf(std::string(asteroidRow["res_fuel"]));
									msg += "\nNahrung: ";
									msg += functions::nf(std::string(asteroidRow["res_food"]));
									msg += "\n";
									
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Asteroidenfeld spionieren",msg);
	
								}
							}
						}
						//gasplanet?
						else if (std::string(codeRow["code"])=="p")
						{
							//Updating GasPlanet
							functions::updateGasPlanet((int)fleet_["entity_to"]);
							
							//Load Data
							query << "SELECT ";
							query << "	planet_res_fuel ";
							query << "FROM ";
							query << "	planets ";
							query << "WHERE ";
							query << "	planet_type_id=7 ";
							query << "	AND id='" << fleet_["entity_to"] << "';";
							mysqlpp::Result gasRes = query.store();
							query.reset();
							
							if (gasRes)
							{
								int gasSize = gasRes.size();
								
								if (gasSize > 0)
								{
									mysqlpp::Row gasRow = gasRes.at(0);
	
									//Nachricht senden
									int fuel = (int)gasRow["planet_res_fuel"];
									std::string msg = "Eine Flotte vom Planeten \n[b]";
									msg += functions::formatCoords((int)fleet_["entity_from"],0),
									msg += "[/b]\nhat [b]den Gas-Planet (";
									msg += functions::formatCoords((int)fleet_["entity_to"],0);
									msg += ")[/b]\num [b]";
									msg += functions::formatTime((int)fleet_["landtime"]);
									msg += "[/b]\n spioniert.\n";
									msg += "\n[b]ROHSTOFFE:[/b]\n\nTritium: ";
									msg += functions::nf(functions::d2s(gasRow["planet_res_fuel"]));
									msg += "\n";
									
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Gas-Planet spionieren",msg);
								}
								else
								{
									//Nachricht senden
									std::string msg = "Eine Flotte vom Planeten \n[b]";
									msg += functions::formatCoords((int)fleet_["entity_from"],0);
									msg += "[/b]\nkonnte [b]kein Gas-Planet [/b]spionieren.\n";
									
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Gas-Planet spionieren",msg);
								}
							}						
						}
						else
						{
							std::string msg = "\n\nEine Flotte vom Planeten ";
							msg += functions::formatCoords((int)fleet_["entity_from"],0);
							msg += " versuchte, das Ziel zu erkunden. Konnte jedoch nur die unendlichen Weiten des leeren Raumes bestaunen.";
							
							functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Erkundungsversuch gescheitert",msg);
						}
					}
				}
			}
			else
			{
				std::string msg = "[b]Planet:[/b] ";
				msg += functions::formatCoords((int)fleet_["entity_to"],0);
				msg += "\n[b]Besitzer:[/b] ";
				msg += functions::getUserNick(userToId);
				msg += "\n\nEine Flotte vom Planeten ";
				msg += functions::formatCoords((int)fleet_["entity_from"],0);
				msg += " versuchte, das Ziel zu erkunden. Leider war kein Schiff mehr in der Flotte, welches erkunden kann, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Erkundungsversuch gescheitert",msg);
			}
		}
			
		// Flotte zurückschicken
  		fleetReturn(1);
	}
}
