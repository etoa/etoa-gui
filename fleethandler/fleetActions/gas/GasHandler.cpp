#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "GasHandler.h"
#include "../../MysqlHandler.h"
#include "../../config/ConfigHandler.h"
#include "../../functions/Functions.h"

namespace gas
{
	void GasHandler::update()
	{
	
		/**
		* Fleet-Action: Gas collect on gas planet
		*/
		
		/** Init data **/
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		
		this->action = std::string(fleet_["action"]);
		
		/** Precheck action==possible? **/
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
				/** Update the gas planet **/
				functions::updateGasPlanet((int)fleet_["entity_to"]);
				
				query << std::setprecision(18);
				
				/** Calculate if and how many ship got destroyed **/
				this->destroyedShips = "";
				this->destroy = 0;
				this->one = rand() % 101;
				this->two = (int)(config.nget("gascollect_action",0) * 100);
				if (this->one  < this->two)	{
					this->destroy = rand() % (int)(config.nget("gascollect_action",1) * 100);
				}
				
				/** if ships got destroyed, calculate how many and write the message part **/
				if(this->destroy>0) {
					query << "SELECT ";
					query << "	s.ship_name, ";
					query << "	fs.fs_ship_id, ";
					query << "	fs.fs_ship_cnt ";
					query << "FROM ";
					query << "(";
					query << "	fleet_ships AS fs ";
					query << "INNER JOIN ";
					query << "	fleet AS f ";
					query << "	ON fs.fs_fleet_id = f.id ";
					query << ")"; 
					query << "INNER JOIN ";
					query << "	ships AS s ";
					query << "	ON fs.fs_ship_id = s.ship_id ";
					query << "	AND f.id='" << fleet_["id"] << "' ";
					query << "GROUP BY ";
					query << "fs.fs_ship_id;";
					mysqlpp::Result cntRes = query.store();
					query.reset();
			
					if (cntRes) {
						int cntSize = cntRes.size();
				
						if (cntSize > 0) {
							mysqlpp::Row cntRow = cntRes.at(0);
					
							for (mysqlpp::Row::size_type i = 0; i<cntSize; i++) {
								cntRow = cntRes.at(i);
								
								/** calculate how many ships got destroyed, per type **/
								this->shipDestroy = (int)floor((int)cntRow["fs_ship_cnt"] * this->destroy / 100);
						
								if(this->shipDestroy>0) {
									/** Delete the destroyed ships from the fleet **/
									query << "UPDATE ";
									query << "	fleet_ships ";
									query << "SET ";
									query << "	fs_ship_cnt=fs_ship_cnt-'" << this->shipDestroy << "' ";
									query << "WHERE ";
									query << "	fs_fleet_id='" << fleet_["id"] << "' ";
									query << "	AND fs_ship_id='" << cntRow["fs_ship_id"] << "';";
									query.store();
									query.reset();
									destroyedShips += functions::d2s(this->shipDestroy);
									destroyedShips += " ";
									destroyedShips += std::string(cntRow["ship_name"]);
									destroyedShips += "\n";
								}
							}
						}
					}
					
					/** The message part **/
					if(this->shipDestroy > 0) {
						this->destroyedShipsMsg = "\n\nAufgrund starker Wasserstoffexplosionen sind einige deiner Schiffe zerst&ouml;rt worden:\n\n";
						this->destroyedShipsMsg += this->destroyedShips;
					}
				}
				
				/** If no ship got destroyed, there is no need for a message part **/
				else{
					this->destroyedShipsMsg = "";
				}
		
				/** load the fuel from the planet **/
				query << "SELECT ";
				query << "	planet_res_fuel ";
				query << "FROM ";
				query << "	planets ";
				query << "WHERE ";
				query << "id='" << fleet_["entity_to"] << "' ";
				query << "	AND planet_type_id='" << config.get("gasplanet",0) << "';";
				mysqlpp::Result fuelRes = query.store();
				query.reset();
		
				fuelTotal = fleet_["res_fuel"];

				if (fuelRes) {
					int fuelSize = fuelRes.size();
			
					if (fuelSize > 0) {
						mysqlpp::Row fuelRow = fuelRes.at(0);
						
						/** Calculate the capacity gas**/
						this->gasCapa = 0;
						this->fleetCapa = 0;
						query << "SELECT ";
						query << "	SUM(ship_capacity*fs_ship_cnt) as capa ";
						query << "FROM ";
						query << "	fleet_ships ";
						query << "INNER JOIN ";
						query << "	ships ON fs_ship_id = ship_id ";
						query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
						query << "	AND fs_ship_faked='0' ";
						query << "	AND ship_actions LIKE '%" << action << "%';";
						mysqlpp::Result gasRes = query.store();
						query.reset();
						
						if (gasRes) {
							int gasSize = gasRes.size();
							
							if (gasSize > 0) {
								mysqlpp::Row gasRow = gasRes.at(0);
								this->gasCapa = (double)gasRow["capa"];
							}
						}
						
						/** Calculate the capacity total **/
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
						
						if (capaRes) {
							int capaSize = capaRes.size();
							
							if (capaSize > 0) {
								mysqlpp::Row capaRow = capaRes.at(0);
								this->fleetCapa = (double)capaRow["capa"]- (double)fleet_["res_metal"] - (double)fleet_["res_crystal"] - (double)fleet_["res_plastic"] - (double)fleet_["res_fuel"] - (double)fleet_["res_food"];
							}
						}

						/** Calculate the collected resources **/
						this->capa = std::min(this->fleetCapa, this->gasCapa);
						this->fuel = 1000 + (rand() % (int)(this->capa - 999));
						this->fuel = std::min(fuel, (double)fuelRow["planet_res_fuel"]);
			
						/** Save the resource on the planet with the new value **/
						this->newFuel = fuelRow["planet_res_fuel"] - this->fuel;
						query << "UPDATE ";
						query << "	planets ";
						query << "SET ";
						query << "	planet_res_fuel='" << this->newFuel << "' ";
						query << "WHERE ";
						query << "	id='" << fleet_["entity_to"] << "';";
						query.store();
						query.reset();

						/** Add the collected fuel to the fuel already in the fleet **/
						this->fuelTotal = this->fuel + fleet_["res_fuel"];
					}
				}

				/** Send fleet back home again **/
				fleetReturn(1,-1,-1,-1,this->fuelTotal,-1,-1);

				/** Send a message to the user **/
				std::string msg = "[b]GASSAUGER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]";
				msg += functions::formatCoords((int)fleet_["entity_from"],0);
				msg += "[/b]\nhat den [b]Gasplaneten (";
				msg += functions::formatCoords((int)fleet_["entity_to"],2);
				msg += ")[/b]\num [b]";
				msg += functions::formatTime((int)fleet_["landtime"]);
				msg += "[/b]\n erreicht und Gas gesaugt.\n";
				
				std::string msgRes = "\n[b]ROHSTOFFE:[/b]\n\nTritium: ";
				msgRes += functions::nf(functions::d2s(fuel));
				msgRes += destroyedShipsMsg;
				msg += msgRes;
				
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Gas gesaugt",msg);
				
				/** Save the collected fuel in the user stats **/
				query << "UPDATE ";
				query << "	users ";
				query << "SET ";
				query << "	user_res_from_nebula=user_res_from_nebula+'" << this->fuel << "' ";
				query << "WHERE ";
				query << "	user_id='" << fleet_["user_id"] << "';";
				query.store();
				query.reset();  
				
				/** Add a log **/
				std::string log = "Eine Flotte des Spielers [B]";
				log += functions::getUserNick((int)fleet_["user_id"]);
				log += "[/B] vom Planeten [b]";
				log += functions::formatCoords((int)fleet_["entity_from"],0);
				log += "[/b] hat den Gasplaneten [b]";
				log += functions::formatCoords((int)fleet_["entity_to"],0);
				log += "[/b] um [b]";
				log += functions::formatTime((int)fleet_["landtime"]);
				log += "[/b] erreicht und Gas gesaugt.\n";
				log += msgRes;
				functions::addLog(13,log,(int)time);
			}
			
			/** if there was no ship in the fleet with the action **/
			else {
				/** Send a message to the user **/
				std::string text = "\n\nEine Flotte vom Planeten ";
				text += functions::formatCoords((int)fleet_["entity_from"],0);
				text += " versuchte, das Ziel zu übernehmen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Gassaugen gescheitert",text);
				
				/** Send fleet back home again **/
				fleetReturn(1);
			}
		}
	}
}
