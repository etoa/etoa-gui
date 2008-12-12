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
		
		/** Precheck action==possible? **/
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	ship_id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ON fs_ship_id = ship_id ";
		query << "	AND fs_fleet_id='" << f->getId() << "' ";
		query << "	AND fs_ship_faked='0' ";
		query << "	AND ship_actions LIKE '%" << f->getAction() << "%';";
		mysqlpp::Result fsRes = query.store();
		query.reset();
				std::cout << "homw\n";
		if (fsRes) {
			int fsSize = fsRes.size();
			
			if (fsSize > 0) {
				/** Update the gas planet **/
						std::cout << "homw\n";
				functions::updateGasPlanet(f->getEntityTo());
						std::cout << "homw\n";
				query << std::setprecision(18);
				
				/** Calculate if and how many ship got destroyed **/
				this->destroyedShips = "";
				this->destroy = 0;
				this->one = rand() % 101;
				this->two = (int)(config.nget("gascollect_action",0) * 100);
				if (this->one  < this->two)	{
					this->destroy = rand() % (int)(config.nget("gascollect_action",1) * 100);
				}
						std::cout << "homw\n";
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
					query << "	AND f.id='" << f->getId() << "' ";
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
									query << "	fs_fleet_id='" << f->getId() << "' ";
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
							std::cout << "homw\n";
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
				query << "id='" << f->getEntityTo() << "' ";
				query << "	AND planet_type_id='" << config.get("gasplanet",0) << "';";
				mysqlpp::Result fuelRes = query.store();
				query.reset();
		
				fuelTotal = f->getResFuel();

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
						query << "	AND fs_fleet_id='" << f->getId() << "' ";
						query << "	AND fs_ship_faked='0' ";
						query << "	AND ship_actions LIKE '%" << f->getAction() << "%';";
						mysqlpp::Result gasRes = query.store();
						query.reset();
						
						if (gasRes) {
							int gasSize = gasRes.size();
							
							if (gasSize > 0) {
								mysqlpp::Row gasRow = gasRes.at(0);
								this->gasCapa = (double)gasRow["capa"];
							}
						}
								std::cout << "homw\n";
						/** Calculate the capacity total **/
						query << "SELECT ";
						query << "	SUM(ship_capacity*fs_ship_cnt) as capa ";
						query << "FROM ";
						query << "	fleet_ships ";
						query << "INNER JOIN ";
						query << "	ships ON fs_ship_id = ship_id ";
						query << "	AND fs_fleet_id='" << f->getId() << "' ";
						query << "	AND fs_ship_faked='0';";
						mysqlpp::Result capaRes = query.store();
						query.reset();
						
						if (capaRes) {
							int capaSize = capaRes.size();
							
							if (capaSize > 0) {
								mysqlpp::Row capaRow = capaRes.at(0);
								this->fleetCapa = (double)capaRow["capa"] - f->getResLoaded();
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
						query << "	id='" << f->getEntityTo() << "';";
						query.store();
						query.reset();

						/** Add the collected fuel to the fuel already in the fleet **/
						this->fuelTotal = this->fuel + f->getResFuel();
					}
				}

				/** Send fleet back home again **/
				fleetReturn(1,-1,-1,-1,this->fuelTotal,-1,-1);
		std::cout << "homw\n";
				/** Send a message to the user **/
				std::string msg = "[b]GASSAUGER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]";
				msg += f->getEntityFromString();
				msg += "[/b]\nhat den [b]Gasplaneten (";
				msg += f->getEntityToString(2);
				msg += ")[/b]\num [b]";
				msg += f->getLandtimeString();
				msg += "[/b]\n erreicht und Gas gesaugt.\n";
				
				std::string msgRes = "\n[b]ROHSTOFFE:[/b]\n\nTritium: ";
				msgRes += functions::nf(functions::d2s(this->fuel));
				msgRes += destroyedShipsMsg;
				msg += msgRes;
						std::cout << "homw\n";
				functions::sendMsg(f->getUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Gas gesaugt",msg);
				
				/** Save the collected fuel in the user stats **/
				query << "UPDATE ";
				query << "	users ";
				query << "SET ";
				query << "	user_res_from_nebula=user_res_from_nebula+'" << this->fuel << "' ";
				query << "WHERE ";
				query << "	user_id='" << f->getUserId() << "';";
				query.store();
				query.reset();  
				
				/** Add a log **/
				std::string log = "Eine Flotte des Spielers [B]";
				log += functions::getUserNick((int)fleet_["user_id"]);
				log += "[/B] vom Planeten [b]";
				log +=  f->getEntityFromString();
				log += "[/b] hat den Gasplaneten [b]";
				log +=  f->getEntityToString();
				log += "[/b] um [b]";
				log +=  f->getLandtimeString();
				log += "[/b] erreicht und Gas gesaugt.\n";
				log += msgRes;
				functions::addLog(13,log,(int)time);
			}
			
			/** if there was no ship in the fleet with the action **/
			else {
				/** Send a message to the user **/
				std::string text = "\n\nEine Flotte vom Planeten ";
				text += f->getEntityFromString();
				text += " versuchte, das Ziel zu übernehmen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
				functions::sendMsg(f->getUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Gassaugen gescheitert",text);
				
				/** Send fleet back home again **/
				fleetReturn(1);
			}
		}
	}
}
