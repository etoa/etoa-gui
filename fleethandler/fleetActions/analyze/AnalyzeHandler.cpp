#include <iostream>
#include <string>
#include <mysql++/mysql++.h>

#include "AnalyzeHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace analyze
{
	void AnalyzeHandler::update()
	{
	
		/**
		* Fleet action: Analyze
		*/
		Config &config = Config::instance();
		this->action = std::string(this->fleet_["action"]);	
		
		std::string coordsTarget = functions::formatCoords(fleet_["entity_to"],0);
		std::string coordsFrom = functions::formatCoords(fleet_["entity_from"],0);
		std::string coordsGas = functions::formatCoords(fleet_["entity_to"],2);

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
				/** Loading entity datas **/
				query << "SELECT ";
				query << "	code ";
				query << "FROM ";
				query << " entities ";
				query << "WHERE ";
				query << "	id='" << fleet_["entity_to"] << "';";
				mysqlpp::Result codeRes = query.store();
				query.reset();
				
				if (codeRes) {
					int codeSize = codeRes.size();
					
					if (codeSize > 0) {
						mysqlpp::Row codeRow = codeRes.at(0);
						
						/** If entity is a neulafield **/
						if (std::string(codeRow["code"])=="n") {
							query << "SELECT ";
							query << "	res_crystal ";
							query << "FROM ";
							query << " nebulas ";
							query << "WHERE ";
							query << "	id='" << fleet_["entity_to"] << "';";
							mysqlpp::Result nebulaRes = query.store();
							query.reset();
							
							if (nebulaRes) {
								int nebulaSize = nebulaRes.size();
								
								if (nebulaSize > 0) {
									mysqlpp::Row nebulaRow = nebulaRes.at(0);
				
									/** Sending a message to the User with the data **/
									std::string msg = "Eine Flotte vom Planeten \n[b]";
									msg += coordsFrom;
									msg += "[/b]\nhat [b]ein ";
									msg += coordsGas;
									msg += "[/b]\num [b]";
									msg += functions::formatTime((int)fleet_["landtime"]);
									msg += "[/b]\n analysiert.\n";
									msg += "\n[b]ROHSTOFFE:[/b]\n\nSilizium: ";
									msg += functions::nf(std::string(nebulaRow["res_crystal"]));
									msg += "\n";
									
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Nebelfeld analysieren",msg);
	
								}
							}
						}
						
						/** If entity is an asteroidfield **/
						else if (std::string(codeRow["code"])=="a") {
							query << "SELECT ";
							query << "	res_metal,res_crystal,res_plastic,res_fuel,res_food ";
							query << "FROM ";
							query << " asteroids ";
							query << "WHERE ";
							query << "	id='" << fleet_["entity_to"] << "';";
							mysqlpp::Result asteroidRes = query.store();
							query.reset();
							
							if (asteroidRes) {
								int asteroidSize = asteroidRes.size();
								
								if (asteroidSize > 0) {
									mysqlpp::Row asteroidRow = asteroidRes.at(0);

									/** Sending a message to the User with the data **/
									std::string msg = "Eine Flotte vom Planeten \n[b]";
									msg += coordsFrom;
									msg += "[/b]\nhat [b]ein ";
									msg += coordsTarget;
									msg += "[/b]\num [b]";
									msg += functions::formatTime((int)fleet_["landtime"]);
									msg += "[/b]\n analysiert.\n";
									msg += "\n[b]ROHSTOFFE:[/b]\n\nTitan: ";
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
									
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Asteroidenfeld analysieren",msg);
								}
							}
						}
						
						/** If entity is a gasplanet **/
						else if (std::string(codeRow["code"])=="p") {
							/** Updating the Gasplanet **/
							functions::updateGasPlanet((int)fleet_["entity_to"]);
							
							query << "SELECT ";
							query << "	planet_res_fuel ";
							query << "FROM ";
							query << "	planets ";
							query << "WHERE ";
							query << "	planet_type_id=7 ";
							query << "	AND id='" << fleet_["entity_to"] << "';";
							mysqlpp::Result gasRes = query.store();
							query.reset();
							
							if (gasRes) {
								int gasSize = gasRes.size();
								
								if (gasSize > 0) {
									mysqlpp::Row gasRow = gasRes.at(0);
	
									/** Sending a message to the User with the data **/
									std::string msg = "Eine Flotte vom Planeten \n[b]";
									msg += coordsFrom;
									msg += "[/b]\nhat den [b]Gasplanet (";
									msg += coordsGas;
									msg += ")[/b]\num [b]";
									msg += functions::formatTime((int)fleet_["landtime"]);
									msg += "[/b]\n analysiert.\n";
									msg += "\n[b]ROHSTOFFE:[/b]\n\nTritium: ";
									msg += functions::nf(functions::d2s((int)gasRow["planet_res_fuel"]));
									msg += "\n";
									
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Gasplanet analysieren",msg);
								}
								
								/**If planet is not a gasplanet **/
								else {
									std::string msg = "Eine Flotte vom Planeten \n[b]";
									msg += coordsFrom;
									msg += "[/b]\nkonnte jedoch [b]keinen Gasplaneten [/b]analysieren.\n";
									
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Analyseversuch gescheitert",msg);
								}
							}						
						}
						
						/** If non of the possible entitys was there **/
						else {
							std::string msg = "\n\nEine Flotte vom Planeten ";
							msg += coordsFrom;
							msg += " versuchte, das Ziel zu analysieren. Konnte jedoch nur die unendlichen Weiten des leeren Raumes bestaunen.";
							
							functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Analyseversuch gescheitert",msg);
						}
					}
				}
			}
			
			/** If no ship with the action was in the fleet **/
			else {
				std::string msg = "\n\nEine Flotte vom Planeten ";
				msg += coordsTarget;
				msg += " versuchte, das Ziel zu analysieren. Leider war kein Schiff mehr in der Flotte, welches erkunden kann, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Analyseversuch gescheitert",msg);
			}
		}
			
		/** Send fleet home **/
  		fleetReturn(1);
	}
}
