#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "GattackHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

#include "../../battle/BattleHandler.h"
namespace gattack
{
	void GattackHandler::update()
	{
	
		/**
		* Fleet-Action: Gas-Attack
		*/
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		
		this->action = std::string(fleet_["action"]);
		
		std::string coordsTarget = functions::formatCoords(fleet_["entity_to"],0);
		std::string coordsFrom = functions::formatCoords(fleet_["entity_from"],0);
		
		BattleHandler *bh = new BattleHandler(con_,fleet_);
		bh->battle();

		/** gas-attack the planet **/
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
						this->tLevel = 0;
			
						/** Load tech level **/
						query << "SELECT ";
						query << "	techlist_current_level ";
						query << "FROM ";
						query << "	techlist ";
						query << "WHERE ";
						query << "	techlist_user_id='" << fleet_["user_id"] << "' ";
						query << "	AND techlist_tech_id='" << config.idget("Gifttechnologie") << "';";
						mysqlpp::Result tRes = query.store();
						query.reset();
			
						if (tRes) {
							int tSize = tRes.size();
				
							mysqlpp::Row tRow;
				
							if (tSize > 0) {
								tRow = tRes.at(0);
					
								this->tLevel = (double)tRow["techlist_current_level"];
							}
						}
				
						/** Calculate the chance **/
						this->one = rand() % 101;
						this->two = config.nget("gasattack_action",0) + ceil(this->shipCnt/10000) + this->tLevel * 5 + bh->specialShipBonusAntrax * 100;

						if (this->one <= this->two) {
							bh->returnFleet = false;
							
							/** Calculate the damage percentage (Max. 95%) **/
							this->temp = std::min((10 + this->tLevel * 3),(int)config.nget("gasattack_action",1));
							this->fak = rand() % temp;
							this->fak += ceil(shipCnt/10000);
				
							/** Load planet people **/
							query << "SELECT ";
							query << "	planet_people ";
							query << "FROM ";
							query << "	planets ";
							query << "WHERE ";
							query << "	id='" << fleet_["fleet_entity_to"] << "';";
							mysqlpp::Result pRes = query.store();
							query.reset();
				
							if (pRes) {
								int pSize = pRes.size();
								
								if (pSize > 0) {
									mysqlpp::Row pRow = pRes.at(0);
									
									/** Calculate surviveor and dead planet people **/
									this->people = round((double)pRow["planet_people"] * this->fak / 100);
									this->rest = round((double)pRow["planet_people"] - this->people);
									
									/** Update the planet with the new value **/
									query << "UPDATE ";
									query << "	planets ";
									query << "SET ";
									query << "	planet_people='" << people << "' ";
									query << "WHERE ";
									query << "	id='" << fleet_["fleet_entity_to"] << "';";
									query.store();
									query.reset();
					
									/** Send a message to the planet and the fleet user **/
									std::string text = "Eine Flotte vom Planet ";
									text += coordsFrom;
									text += " hat einen Giftgasangriff auf den Planeten ";
									text += coordsTarget;
									text += " verübt es starben dabei ";
									text += functions::nf(functions::d2s(rest));
									text += " Bewohner";
						
									this->userToId = functions::getUserIdByPlanet((int)fleet_["fleet_entity_to"]);
									functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Giftgasangriff",text);
									functions::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Giftgasangriff",text);

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
						
						/** if the action failed **/
						else  {
							/** Send a message to the users **/
							std::string text = "Eine Flotte vom Planet ";
							text += coordsFrom;
							text += " hat erfolglos einen Giftgasangriff auf den Planeten ";
							text += coordsTarget;
							text += " verübt.";
				
							functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Giftgasangriff erfolglos",text);
							functions::sendMsg(this->userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Giftgasangriff erfolglos",text);
						}
					}
					/** If no ship with the action was in the fleet **/
					else {
						std::string text = "Eine Flotte vom Planeten ";
						text += coordsFrom;
						text += " versuchte eine Giftgasangriff auszuführen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
						functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Giftgasangriff gescheitert",text);
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
