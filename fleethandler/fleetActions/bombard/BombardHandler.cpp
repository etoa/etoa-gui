#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "BombardHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"
#include "../../battle/BattleHandler.h"

namespace bombard
{
	void BombardHandler::update()
	{
	
		/**
		* Fleet-Action: Bombard
		*/
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		
		this->action = std::string(fleet_["action"]);

		std::string coordsTarget = functions::formatCoords(fleet_["entity_to"],0);
		std::string coordsFrom = functions::formatCoords(fleet_["entity_from"],0);
						
		/** Calculate the fight **/
		BattleHandler *bh = new BattleHandler(con_,fleet_);
		bh->battle();

		/** Bombard the planet **/
		if (bh->returnV==1) {
			bh->returnFleet = true;
			
			// Precheck action==possible?
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	SUM(fs_ship_cnt) AS cnt ";
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
					mysqlpp::Row fsRow = fsRes.at(0);
					this->shipCnt = fsRow["cnt"];
					
					if (shipCnt > 0) {
						/** Calculate the tech level **/
						this->tLevel = 0;
						query << "SELECT "; 
						query << "	techlist_current_level ";
						query << "FROM ";
						query << "	techlist ";
						query << "WHERE ";
						query << "	techlist_user_id='" << fleet_["user_id"] << "' ";
						query << "	AND techlist_tech_id='" << config.idget("BOMB_TECH_ID") << "'";
						mysqlpp::Result tRes = query.store();
						query.reset();
			
						if (tRes) {
							int tSize = tRes.size();
				
							if (tSize > 0) {
								mysqlpp::Row tRow = tRes.at(0);
								this->tLevel = (int)tRow["techlist_current_level"];
							}
						}
				 
	
						// 10% + Bonis, dass Bombardierung erfolgreich
						this->one = rand() % 101;
						this->two = config.nget("ship_bomb_factor",1) + (config.nget("ship_bomb_factor",0) * this->tLevel + ceil(this->shipCnt / 10000) + bh->specialShipBonusBuildDestroy * 100);
						if (this->one <= this->two) {
							bh->returnFleet = false;
							
							/** SELECT a building **/
							query << "SELECT ";
							query << "	buildlist_id, ";
							query << "	buildlist_building_id, ";
							query << "	buildlist_current_level ";
							query << "FROM ";
							query << "	buildlist ";
							query << "WHERE ";
							query << "	buildlist_planet_id='" << fleet_["entity_to"] << "' ";
							query << "	AND buildlist_current_level>'0' ";
							query << "	AND buildlist_build_type='0' ";
							query << "ORDER BY ";
							query << "	RAND() ";
							query << "LIMIT 1;";
							mysqlpp::Result blRes = query.store();
							query.reset();
				
							if (blRes) {
								int blSize = blRes.size();
					
								if (blSize > 0) {
									mysqlpp::Row blRow = blRes.at(0);
						
									/** level the building down, at least one level **/
									this->bLevel = (int)blRow["buildlist_current_level"] - ceil(this->shipCnt/2500);
	                
									/** SELECT the building name **/
									query << "SELECT ";
									query << "	building_name ";
									query << "FROM ";
									query << "	buildings ";
									query << "WHERE ";
									query << "building_id='" << blRow["buildlist_building_id"] << "';";
									mysqlpp::Result bRes = query.store();
									query.store();
						
									if (bRes) {
										int bSize = bRes.size();
							
										if (bSize > 0) {
											mysqlpp::Row bRow = bRes.at(0);
	                
											/** Update the building with the new level **/
											query << "UPDATE ";
											query << "	buildlist ";
											query << "SET ";
											query << "buildlist_current_level='" << this->bLevel << "' ";
											query << "WHERE ";
											query << "buildlist_id=" << blRow["buildlist_id"] << "";
											query.store();
											query.reset();
											
											/** Send a message to the planet and the fleet user **/
											text = "Eine Flotte vom Planet ";
											text += coordsFrom;
											text += " hat das Gebäude ";
											text += std::string(bRow["building_name"]);
											text += " des Planeten ";
											text += coordsTarget;
											text += " um ein Level auf Stufe ";
											text += std::string(blRow["buildlist_current_level"]);
											text += " zurück gesetzt";

											this->userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
											functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Gebäude bombardiert",text);
											functions::sendMsg(this->userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Gebäude bombardiert",text);
	                
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
						}
						/** If bombard failed **/
						else {
							text = "Eine Flotte vom Planet ";
							text += coordsFrom;
							text += " hat erfolglos versucht ein Gebäude des Planeten ";
							text += coordsTarget;
							text += " um ein Level zu senken";
							functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Bombardierung gescheitert",text);
							functions::sendMsg(this->userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Bombardierung erfolglos",text);
						}
					}
					/** If no ship with the action was in the fleet **/
					else {
						std::string text = "Eine Flotte vom Planeten ";
						text += coordsFrom;
						text += " versuchte eine Bombadierung auszuführen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
						functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Bombardierung gescheitert",text);
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

