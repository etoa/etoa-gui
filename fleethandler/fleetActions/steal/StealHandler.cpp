#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "StealHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

#include "../../battle/BattleHandler.h"

namespace steal
{
	void StealHandler::update()
	{
	
		/**
		* Fleet-Action: Spy-Attack (Steal technology)
		*/

		/** Initialize data **/
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		
		this->action = std::string(fleet_["action"]);
		
		std::string coordsTarget = functions::formatCoords(fleet_["entity_to"],0);
		std::string coordsFrom = functions::formatCoords(fleet_["entity_from"],0);
		this->userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		
		BattleHandler *bh = new BattleHandler(con_,fleet_);
		bh->battle();

		/** Antrax the planet **/
		if (bh->returnV==1) {
			bh->returnFleet = true;
			
			/** Precheck action==possible? **/
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	SUM(fs_ship_cnt) AS cnt ";
			query << "FROM ";
			query << "	fleet_ships ";
			query << "INNER JOIN ";
			query << "	ships ";
			query << "ON fs_ship_id = ship_id ";
			query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
			query << "	AND fs_ship_faked='0' ";
			query << "	AND ship_actions LIKE '%" << this->action << "%';";
			mysqlpp::Result fsRes = query.store();
			query.reset();
		
			if (fsRes) {
				int fsSize = fsRes.size();
				
				if (fsSize > 0) {
					mysqlpp::Row fsRow = fsRes.at(0);
					
					this->shipCnt = (int)fsRow["cnt"];
					
					if (this->shipCnt > 0) {
						this->tLevelAtt = 0;
						this->tLevelDef = 0;
			
						/** Load tech level **/
						query << "SELECT ";
						query << "	techlist_current_level ";
						query << "FROM ";
						query << "	techlist ";
						query << "WHERE ";
						query << "	techlist_user_id='" << fleet_["user_id"] << "' ";
						query << "	AND techlist_tech_id='" << config.idget("SPY_TECH_ID") << "';";
						mysqlpp::Result tRes = query.store();
						query.reset();
			
						if (tRes) {
							int tSize = tRes.size();
				
							mysqlpp::Row tRow;
				
							if (tSize > 0) {
								tRow = tRes.at(0);
					
								this->tLevelAtt = (double)tRow["techlist_current_level"];
							}
						}	
            	
						query << "SELECT ";
						query << "	techlist_current_level ";
						query << "FROM ";
						query << "	techlist ";
						query << "WHERE ";
						query << "	techlist_user_id='" << this->userToId << "' ";
						query << "	AND techlist_tech_id='" << config.idget("SPY_TECH_ID") << "';";
						tRes = query.store();
						query.reset();
			
						if (tRes) {
							int tSize = tRes.size();
				
							mysqlpp::Row tRow;
				
							if (tSize > 0) {
								tRow = tRes.at(0);
					
								this->tLevelDef = (double)tRow["techlist_current_level"];
							}
						}

						/** Calculate the chance **/
						this->one = rand() % 101;
						this->two = 3 + std::min(0.0,(this->tLevelAtt - this->tLevelDef + ceil(this->shipCnt/10000)+ bh->specialShipBonusForsteal * 100));
						if (this->one <= this->two) {
							/** Select a tech by random, the fleet user has to have the tech already, tech doesnt have to research it at the moment and 
							* and the level should be higher then the actual level at home **/
							query << "SELECT ";
							query << "	t.tech_name, ";
							query << "	t.tech_last_level, ";
							query << "	def.techlist_tech_id, ";
							query << "	def.techlist_current_level AS def_techlist_current_level, ";
							query << "	att.techlist_current_level AS att_techlist_current_level ";
							query << "FROM ";
							query << "	technologies AS t ";
							query << "INNER JOIN ";
							query << "(";
							query << "		techlist AS def ";
							query << "		INNER JOIN ";
							query << "			techlist AS att ";
							query << "			ON def.techlist_tech_id = att.techlist_tech_id ";
							query << "			AND att.techlist_build_type!=3 ";
							query << "			AND def.techlist_user_id='" << this->userToId << "' ";
							query << "			AND att.techlist_user_id=" << fleet_["user_id"] << " ";
							query << "			AND def.techlist_current_level>att.techlist_current_level ";
							query << "			AND att.techlist_current_level>0 ";
							query << ")";
							query << "	ON t.tech_id = def.techlist_tech_id ";
							query << "	AND t.tech_stealable = '1' ";
							query << "ORDER BY ";
							query << "	RAND() ";
							query << "LIMIT 1;";
							mysqlpp::Result techRes = query.store();
							query.reset();
				
							if (techRes) {
								int techSize = techRes.size();
					
								if (techSize > 0) {
									mysqlpp::Row techRow = techRes.at(0);
						
									/** End the research if the new level would be higher then the max level **/
									if((int)techRow["def_techlist_current_level"]==(int)techRow["tech_last_level"]) {
										query << "UPDATE ";
										query << "	techlist ";
										query << "SET ";
										query << "	techlist_current_level='" << techRow["def_techlist_current_level"] << "', ";
										query << "	techlist_build_type='0', ";
										query << "	techlist_build_start_time='0', ";
										query << "	techlist_build_end_time='0' ";
										query << "WHERE ";
										query << "	techlist_user_id=" << fleet_["user_id"] << " ";
										query << "	AND techlist_tech_id=" << techRow["techlist_tech_id"] << ";";
										query.store();
										query.reset();
									}
									/** if not update the tech **/
									else {
										query << "UPDATE ";
										query << "	techlist ";
										query << "SET ";
										query << "	techlist_current_level=" << techRow["def_techlist_current_level"] << " ";
										query << "WHERE ";
										query << "	techlist_user_id=" << fleet_["user_id"] << " ";
										query << "	AND techlist_tech_id=" << techRow["techlist_tech_id"] << ";";
										query.store();
										query.reset();
									}
									
									/** Send a message to the fleet and planer user **/
									std::string text = "Eine Flotte vom Planeten ";
									text += coordsFrom;
									text += " hat erfolgreich einen Spionageangriff durchgeführt und erfuhr so die Geheimnisse der Forschung ";
									text += std::string(techRow["tech_name"]);
									text += " bis zum Level ";
									text += std::string(techRow["def_techlist_current_level"]);
									text += "\n";
									
									functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Spionageangriff",text);
									functions::sendMsg(this->userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Spionageangriff",text);
									
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

						/** if spy attack failed **/
						else  {
							std::string text = "Eine Flotte vom Planet ";
							text += coordsFrom;
							text += " hat erfolglos einen Spionageangriff auf den Planeten ";
							text += coordsTarget;
							text += " verübt.";
							functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Spionageangriff erfolglos",text);
							functions::sendMsg(this->userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Spionageangriff erfolglos",text);
						}
					}
					/** If no ship with the action was in the fleet **/
					else {
						std::string text = "Eine Flotte vom Planeten ";
						text += coordsFrom;
						text += " versuchte eine Spionageangriff auszuführen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
						functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Spionageangriff gescheitert",text);
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
