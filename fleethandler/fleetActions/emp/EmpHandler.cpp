#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "EmpHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

#include "../../battle/BattleHandler.h"

namespace emp
{
	void EmpHandler::update()
	{
	
		/**
		* Fleet-Action: EMP-Attack
		*/
		
		/** Initialize some stuff **/
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		this->action = std::string(fleet_["action"]);
		
		std::string coordsTarget = functions::formatCoords(fleet_["entity_to"],0);
		std::string coordsFrom = functions::formatCoords(fleet_["entity_from"],0);

		/** Calculate the battle **/
		BattleHandler *bh = new BattleHandler(con_,fleet_);
		bh->battle();

		/** If the attacker is the winner, deactivade a building **/
		if (returnV==1) {
			bh->returnFleet = true;
			
			/** Precheck action==possible? **/
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	SUM(fs_ship_cnt) AS cnt ";
			query << "FROM ";
			query << "	fleet_ships ";
			query << "INNER JOIN ";
			query << "	ships ON fs_ship_id = ship_id ";
			query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
			query << "	AND fs_ship_faked='0' ";
			query << "	AND ship_actions LIKE '%" << action << "%';";
			mysqlpp::Result fsRes = query.store();
			query.reset();
					
			if (fsRes) {
				int fsSize = fsRes.size();
				
				if (fsSize > 0) {
					mysqlpp::Row fsRow = fsRes.at(0);
					
					this->shipCnt = fsRow["cnt"];
					
					if (this->shipCnt > 0) {
						/** Load the emp technology of the fleet user **/
						this->tLevel = 0;
						query << "SELECT ";
						query << "	techlist_current_level ";
						query << "FROM ";
						query << "	techlist ";
						query << "WHERE ";
						query << "	techlist_user_id='" << fleet_["user_id"] << "' ";
						query << "	AND techlist_tech_id='17'";
						mysqlpp::Result tRes = query.store();
						query.reset();
			
						if (tRes) {
							int tSize = tRes.size();
				
							if (tSize > 0) {
								mysqlpp::Row tRow = tRes.at(0);
								this->tLevel = (int)tRow["techlist_current_level"];
							}
						}
		
						/** Calculate the possibility **/
						this->one = rand() % 101;
						this->two = 10 + ceil(this->shipCnt/10000) + this->tLevel * 5 + bh->specialShipBonusAntrax * 100;
						if (this->one <= this->two) {
							bh->returnFleet = false;
							
							/** Calculate the damage **/
							this->h = rand() % (10 + this->tLevel + 1);
							if (this->tLevel==0) {
								this->tLevel = 1;
							}

							/** Load a building by random **/
							query << "SELECT ";
							query << "	buildlist_deactivated, ";
							query << "	buildlist_building_id ";
							query << "FROM ";
							query << "buildlist ";
							query << "WHERE ";
							query << "	buildlist_planet_id='" << fleet_["entity_to"] << "' ";
							query << "	AND buildlist_current_level > 0 ";
							query << "	AND (";
							query << "		buildlist_building_id='" << config.idget("FLEET_CONTROL_ID") << "' ";
							query << "		OR buildlist_building_id='" << config.idget("FACTORY_ID") << "' ";
							query << "		OR buildlist_building_id='" << config.idget("SHIPYARD_ID") << "' ";
							query << "		OR buildlist_building_id='" << config.idget("BUILD_MISSILE_ID") << "' ";
							query << "		OR buildlist_building_id='" << config.idget("BUILD_CRYPTO_ID") << "' ";
							query << "		)";
							query << "ORDER BY ";
							query << "	RAND() ";
							query << "LIMIT 1;";
							mysqlpp::Result bRes = query.store();
							query.reset();
				
							if (bRes) {
								int bSize = bRes.size();
								
								if (bSize > 0) {
									mysqlpp::Row bRow = bRes.at(0);

									/** Calculate the time, while the building is deactivated **/
									this->time = std::max((int)fleet_["landtime"],(int)bRow["buildlist_deactivated"]);
									this->time2Add = this->time + this->h;

									/** Update the deactivated building **/
									query << "UPDATE ";
									query << "	buildlist ";
									query << "SET ";
									query << "	buildlist_deactivated='" << this->time2Add << "' ";
									query << "WHERE ";
									query << "	buildlist_planet_id='" << fleet_["entity_to"] << "' ";
									query << "	AND buildlist_building_id='" << bRow["buildlist_building_id"] << "'";
									query.store();
									query.reset();
									
									/** Select the deactivated building **/
									query << "SELECT ";
									query << "	building_name ";
									query << "FROM ";
									query << "	buildings ";
									query << "WHERE ";
									query << "	building_id='" << bRow["buildlist_building_id"] << "';";
									mysqlpp::Result nameRes = query.store();
									query.reset();
					
									if (nameRes) {
										int nameSize = nameRes.size();
					 
										if (nameSize > 0) {
											mysqlpp::Row nameRow = nameRes.at(0);
											
											/** Send messages to the planet and the fleet user **/
											std::string text = "Eine Flotte vom Planet ";
											text += coordsFrom;
											text += " hat das Gebäude ";
											text += std::string(bRow["building_name"]);
											text += " des Planeten ";
											text += coordsTarget;
											text += " für ";
											text += this->h;
											text += "h deaktiviert.";
											
											this->userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
											functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"GebäDeaktivierung",text);
											functions::sendMsg(this->userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Deaktivierung",text);
	                
											//Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo

											/** Delete one ship and check if there are still some ship in the fleet, if not delete it **/
											query << "SELECT ";
											query << "	fs_id, ";
											query << "	fs_ship_cnt ";
											query << "FROM ";
											query << "	fleet_ships ";
											query << "INNER JOIN ";
											query << "	ships ";
											query << "ON fs_ship_id = ship_id ";
											query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
											query << "	AND fs_ship_faked='0' ";
											query << "	AND ship_actions LIKE '%" << this->action << "%' ";
											query << "ORDER BY ";
											query << "RAND() ";
											query << "LIMIT 1;";
											mysqlpp::Result sRes = query.store();
											query.reset();
									
											if (sRes) {
												int sSize = sRes.size();
												
												if (sSize>0) {
													mysqlpp::Row sRow = sRes.at(0);
											
													/** if ship type has only one ship delete it in the DB, else update the entry **/
													if ((int)sRow["fs_ship_cnt"] <= 1) {
														query << "DELETE FROM ";
														query << "	fleet_ships ";
														query << "WHERE ";
														query << "	fs_id='" << sRow["fs_id"] << "' ";
														query << "LIMIT 1;";
														query.store();
														query.reset();
													}
													else {
														query << "UPDATE ";
														query << "	fleet_ships ";
														query << "SET ";
														query << "	fs_ship_cnt='" << ((int)sRow["fs_ship_cnt"] - 1) << "' ";
														query << "WHERE ";
														query << "	fs_id='" << fsRow["fs_id"] << "' ";
														query << "LIMIT 1;";
														query.store();
														query.reset();
													}
													
													/** Check if there are still some ships in the fleet **/
													query << "SELECT ";
													query << " SUM(fs_ship_cnt) AS cnt ";
													query << "FROM ";
													query << "	fleet_ships ";
													query << "WHERE ";
													query << "	fs_fleet_id='" << fleet_["id"] << "';";
													mysqlpp::Result saRes = query.store();
													query.reset();
									
													if (saRes) {
														int saSize = saRes.size();
										
														if (saSize > 0) {
															mysqlpp::Row saRow = saRes.at(0);
											
															/** If there are still some ships in the fleet, update the send home flag to true **/
															if ((int)saRow["cnt"]>0) bh->returnFleet = true;
														}
													}
												}
											}
										}
									}
								}
							}
							
							/** If there exists no building to deactivade, send a message to the planet and the fleet user **/
							else {
								std::string text = "Eine Flotte vom Planet ";
								text += coordsFrom;
								text += " hat erfolglos versucht auf dem Planeten ";
								text += coordsTarget;
								text += " ein Gebäude zu deaktivieren.\nHinweis: Der Spieler hat keine Gebäudeeinrichtungen, welche deaktiviert werden können!";
								functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Deaktivierung erfolglos",text);
								functions::sendMsg(this->userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Deaktivierung erfolglos",text);
							}
						}
						
						/** If the deactivation failed, send a message to the planet and the fleet user **/
						else {
							std::string text = "Eine Flotte vom Planet ";
							text += coordsFrom;
							text += " hat erfolglos versucht auf dem Planeten ";
							text += coordsTarget;
							text += " ein Gebäude zu deaktivieren.";
							functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Deaktivierung erfolglos",text);
							functions::sendMsg(this->userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Deaktivierung erfolglos",text);
						}
					}
					/** If no ship with the action was in the fleet **/
					else {
						std::string text = "Eine Flotte vom Planeten ";
						text += coordsFrom;
						text += " versuchte ein Gebäude zu deaktivieren. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
						functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Deaktivierung gescheitert",text);
					}
				}
			}
		}
		
		/** Send fleet home or delete it **/
		if (bh->returnFleet || bh->returnV==4) {
			fleetReturn(1);
		}
		else {
			fleetDelete();
		}
	}
}
