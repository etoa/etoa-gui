#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "NebulaHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace nebula
{
	void NebulaHandler::update()
	{
	
		/**
		* Fleet action: Collect nebula gas
		*/

		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		this->action = std::string(fleet_["action"]);

		//Precheck action==possible?
		mysqlpp::Query query = con_->query();
		query << std::setprecision(18);
		query << "SELECT ";
		query << "	ship_id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ON fs_ship_id = ship_id ";
		query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
		query << "	AND fs_ship_faked='0'";
		query << "	AND ship_actions LIKE '%" << this->action << "%';";
		mysqlpp::Result fsRes = query.store();
		query.reset();

		if (fsRes) {
			int fsSize = fsRes.size();

			if (fsSize > 0) {	
				/** Check if the field still exists **/
				query << "SELECT ";
				query << "	res_crystal ";
				query << "FROM ";
				query << "nebulas "; 
				query << "WHERE ";
				query << "	id='" << fleet_["entity_to"] << "';";
				mysqlpp::Result nebulaRes = query.store();
				query.reset();

				if (nebulaRes) {
					int nebulaSize = nebulaRes.size();
					
					if (nebulaSize > 0) {
						mysqlpp::Row nebulaRow = nebulaRes.at(0);

						this->destroyedShips = "";
						this->destroy = 0;
						this->one = rand() % 101;
						this->two = (int)(config.nget("nebula_action",0) * 100);

						if (this->one  < this->two) {
							this->destroy = rand() % (int)(config.nget("nebula_action",1) * 100);
						}
						
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
									
									for (mysqlpp::Row::size_type i = 0; i<cntSize; i++)  {
										cntRow = cntRes.at(i);
										
										/** Calculate how many ships got destroyed **/
										this->shipDestroy = (int)floor((int)cntRow["fs_ship_cnt"] * this->destroy / 100);
										
										/** If ships got destroyed, delete them from the fleet and prepare the message **/
										if(this->shipDestroy>0) {
											query << "UPDATE ";
											query << "	fleet_ships ";
											query << "SET ";
											query << "	fs_ship_cnt=fs_ship_cnt-'" << this->shipDestroy << "' ";
											query << "WHERE ";
											query << "	fs_fleet_id='" << fleet_["id"] << "' ";
											query << "	AND fs_ship_id='" << cntRow["fs_ship_id"] << "';";
											query.store();
											query.reset();
											
											this->destroyedShips += functions::d2s(this->shipDestroy);
											this->destroyedShips += " ";
											this->destroyedShips += std::string(cntRow["ship_name"]);
											this->destroyedShips += "\n";
										}
									}
								}
							}
							
							/** Add the header for this part message **/
							if(this->shipDestroy > 0) {
								this->destroyedShipsMsg = "\n\nEinige Schiffe deiner Flotte verirrten sich in einem Interstellarer Gasnebel und konnten nicht mehr gefunden werden.:n\n";
								this->destroyedShipsMsg += this->destroyedShips;
							}
						}
						
						/** if no ship got destroyed, there is no need for a message **/
						else{
							this->destroyedShipsMsg = "";
						}
						
						/** Calculate the cpacitys **/
						this->fleetCapa = 0;
						this->nebulaCapa = 0;
						query << "SELECT ";
						query << "	SUM(ship_capacity*fs_ship_cnt) as capa ";
						query << "FROM ";
						query << "	fleet_ships ";
						query << "INNER JOIN ";
						query << "	ships ON fs_ship_id = ship_id ";
						query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
						query << "	AND fs_ship_faked='0' ";
						query << "	AND ship_actions LIKE '%" << this->action << "%';";
						mysqlpp::Result nebulaRes = query.store();
						query.reset();
						
						if (nebulaRes) {
							int nebulaSize = nebulaRes.size();
						
							if (nebulaSize > 0) {
								mysqlpp::Row nebulaRow = nebulaRes.at(0);
									
								this->nebulaCapa = (int)nebulaRow["capa"];
							}
						}			
										
						/** Check if there are still some nebula collecter in the fleet **/
						if (this->nebulaCapa > 0) {
							query << "SELECT ";
							query << "	SUM(ship_capacity*fs_ship_cnt) as capa ";
							query << "FROM ";
							query << "	fleet_ships ";
							query << "INNER JOIN ";
							query << "	ships ON fs_ship_id = ship_id ";
							query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
							query << "	AND fs_ship_faked='0' ";
							mysqlpp::Result capaRes = query.store();
							query.reset();
							
							if (capaRes) {
								int capaSize = capaRes.size();
			
								if (capaSize > 0) {
									mysqlpp::Row capaRow = capaRes.at(0);
									this->fleetCapa = (double)capaRow["capa"];
								}
							}
							
							/** Calculate the collected res **/
							this->capa = std::min(this->fleetCapa, this->nebulaCapa);
							
							this->maxRess = (int)nebulaRow["res_crystal"];
							
							this->nebula = config.nget("nebula_action",1) + (rand() % (int)(this->capa - config.nget("nebula_action",1) + 1));
							this->crystal = round(std::min(this->nebula, this->maxRess));
							
							this->resTotal = (int)nebulaRow["res_crystal"] -this->crystal;
							
							/** Update the nebula field with the new resources **/
							query << "UPDATE ";
							query << "	nebulas ";
							query << "SET ";
							query << "	res_crystal='" << this->resTotal << "' ";
							query << "WHERE ";
							query << "	id='" << fleet_["entity_to"] << "';";
							query.store();
							query.reset();
							
							/** Check if there are still enough resources in the field, if not delete it and create a new one **/
							query << "SELECT ";
							query << "	res_crystal ";
							query << "FROM ";
							query << "	nebulas ";
							query << "WHERE ";
							query << "id='" << fleet_["entity_to"] << "';";
							mysqlpp::Result checkRes = query.store();
							query.reset();

							if (checkRes) {
								int checkSize = checkRes.size();
								
								if (checkSize > 0) {
									mysqlpp::Row checkRow = checkRes.at(0);
									
									/** if there are not enough resources in the field any more ... **/
									if ((int)checkRow["res_crystal"] < config.nget("nebula_action",2)) {
										/** Delete the old one and replace it with an empty field **/
										query << "UPDATE ";
										query << "	entities ";
										query << "SET ";
										query << "	code='e', ";
										query << " lastvisited='0' ";
										query << "WHERE ";
										query << "	id='" << fleet_["entity_to"] << "';";
										query.store();
										query.reset();
										
										query << "DELETE FROM";
										query << "	nebulas ";
										query << "WHERE ";
										query << " id='" << fleet_["entity_to"] << "';";
										query.store();
										query.reset();
										
										query << "INSERT INTO ";
										query << " space ";
										query << "(";
										query << "	id ";
										query << ") ";
										query << "VALUES ";
										query << "(";
										query << "'" << fleet_["entity_to"] << "');";
										query.store();
										query.reset();
										
										/** Create a new one **/
										this->newRess = config.nget("nebula_ress",1) + (rand() % (int)(config.nget("nebula_ress",2) - config.nget("nebula_ress",1) + 1));

										/** Check if there is an empty field left **/
										query << "SELECT ";
										query << "	id ";
										query << "FROM ";
										query << "	entities ";
										query << "WHERE ";
										query << "	code='e' ";
										query << "ORDER BY ";
										query << " RAND() ";
										query << "LIMIT 1;";
										mysqlpp::Result searchRes = query.store();
										query.reset();
										
										if (searchRes) {
											int searchSize = searchRes.size();
											
											/** if there is, create it **/
											if (searchSize > 0) {
												mysqlpp::Row searchRow = searchRes.at(0);
												
												query << "UPDATE ";
												query << "	entities ";
												query << "SET ";
												query << "	code='n' ";
												query << "WHERE ";
												query << "	id='" << searchRow["id"] << "';";
												query.store();
												query.reset();
												
												query << "INSERT INTO ";
												query << "	nebulas ";
												query << "(";
												query << "	id, ";
												query << "	res_crystal ";
												query << ") ";
												query << "VALUES ";
												query << "(";
												query << "'" << searchRow["id"] << "', ";
												query << "'" << this->newRess << "');";
												query.store();
												query.reset();
												
												query << "DELETE FROM ";
												query << "	space ";
												query << "WHERE ";
												query << " id='" << searchRow["id"] << "';";
												query.store();
												query.reset();
											}
										}
									}
									
									/** Add the collected resources to the fleet resources **/
									this->crystal += (double)fleet_["res_crystal"];
									
									/** Send fleet back home **/
									fleetReturn(1,-1,this->crystal,-1,-1,-1,-1);
									
									/** Send a message to the fleet user **/
									std::string msg = "Eine Flotte vom Planeten \n[b]";
									msg += functions::formatCoords((int)fleet_["entity_from"],0);
									msg +="[/b]\nhat [b]einen Interstellarer Gasnebel [/b]\num [b]";
									msg += functions::formatTime((int)fleet_["landtime"]);
									msg += "[/b]\n erkundet und dabei Rohstoffe gesammelt.\n";
									msgRes = "\n[b]ROHSTOFFE:[/b]\n\nSilizium: ";
									msgRes += functions::nf(functions::d2s(this->crystal));
									msgRes += "\n";
									msg += msgRes;
									msg += this->destroyedShipsMsg;
									
									functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Nebelfeld erkunden",msg);
									
									/** Save the collected resources in the fleet stats **/
									query << "UPDATE ";
									query << "	users ";
									query << "SET ";
									query << "	user_res_from_nebula=user_res_from_nebula+'" << this->crystal << "' ";
									query << "WHERE ";
									query << "	user_id='" << fleet_["user_id"] << "';";
									query.store();
									query.reset();
									
									/** Add a log **/
									std::string log = "Eine Flotte des Spielers [B]";
									log += functions::getUserNick((int)fleet_["user_id"]);
									log += "[/B] vom Planeten [b]";
									log += functions::formatCoords((int)fleet_["entity_from"],0);
									log += "[/b] at [b]einen Interstellarer Gasnebel [/b] um [b]";
									log += functions::formatTime((int)fleet_["landtime"]);
									log += "[/b]\n erkundet und dabei Rohstoffe gesammelt.\n";
									log += msgRes;
									log += this->destroyedShipsMsg;
									functions::addLog(13,log,time);
								}
							}
						}
				
						/** if there are no nebula collecter in the fleet anymore **/
						else {
							/** Send a message to the fleet user and delete the flee **/
							std::string msg = "Eine Flotte vom Planeten \n[b]";
							msg += functions::formatCoords((int)fleet_["entity_from"],0);
							msg += "[/b]\n verirrte sich in einem Interstellarer Gasnebel.";

							functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Flotte verschollen",msg);

							/** Add a log **/
							std::string log = "Eine Flotte des Spielers [B]";
							log += functions::getUserNick((int)fleet_["user_id"]),
							log += "[/B] vom Planeten [b]";
							log += functions::formatCoords((int)fleet_["entity_from"],0);
							log += "[/b] verirrte sich in einem Interstellarer Gasnebel.";
					
							functions::addLog(13,log,time);

							/** Delete the fleet **/
							fleetDelete();
						}
					}
					
					/** if the nebula field doesnt exist anmore **/
					else {
						/** Send the fleet back home again **/
						fleetReturn(1);
						
						/** Send a message to the fleet user **/
						std::string msg = "Die Flotte vom Planeten \n[b]";
						msg += functions::formatCoords((int)fleet_["entity_from"],0);
						msg += "[/b]\n konnte kein Intergalaktisches Nebelfeld orten.\n";
						
						functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Nebelfeld verschwunden",msg);
						
						/** Add a log **/
						std::string log = "Eine Flotte des Spielers [B]";
						log += functions::getUserNick((int)fleet_["user_id"]);
						log += "[/B] vom Planeten [b]";
						log += functions::formatCoords((int)fleet_["entity_from"],0);
						log += "[/b] konnte kein Intergalaktisches Nebelfeld orten.";
						functions::addLog(13,log,time);
					}
				}
			}
			
			/** if there was already in the beging no collecter in the fleet **/
			else {
				/** Send a message to the user **/
				std::string text = "Eine Flotte vom Planeten ";
				text += functions::formatCoords((int)fleet_["entity_from"],0);
				text += " versuchte, Nebel zu sammeln. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
				
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Nebelsammeln gescheitert",text);
				
				/** Send fleet back home again **/
				fleetReturn(1);
			}
		}
	}
}
