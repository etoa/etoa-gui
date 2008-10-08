#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "InvadeHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

#include "../../battle/BattleHandler.h"

namespace invade
{
	void InvadeHandler::update()
	{
	
		/**
		* Fleet-Action: Invade
		*/
		/** Initialize data **/
		Config &config = Config::instance();
		this->time = std::time(0);
		srand (this->time);
		
		this->action = std::string(fleet_["action"]);
		this->userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		
		std::string coordsTarget = functions::formatCoords(fleet_["entity_to"],0);
		std::string coordsFrom = functions::formatCoords(fleet_["entity_from"],0);
		
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
						/** Load current user data **/
						query << "SELECT ";
						query << "	planet_user_id, ";
						query << "	planet_user_main ";
						query << "FROM ";
						query << "	planets ";
						query << "WHERE ";
						query << "	id='" << fleet_["entity_to"] << "'";
						mysqlpp::Result checkRes = query.store();
						query.reset();
		
						if (checkRes) {
							int checkSize = checkRes.size();
			
							if (checkSize > 0) {
								mysqlpp::Row checkRow = checkRes.at(0);
				
								/** Check if the planet user is the same as the fleet user **/
								if((int)checkRow["planet_user_id"]==(int)fleet_["user_id"]) {
									/** Land fleet and delete it from the db **/
									fleetLand(1,0,1);
									fleetDelete();

									/** Send a message to the user **/
									std::string msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
									msg += coordsTarget;
									msg += "\n[b]Zeit:[/b] ";
									msg += functions::formatTime((int)fleet_["landtime"]);
									msg += "\n[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!";
									msg += msgAllShips;
									msg += msgRes;
									functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Flotte angekommen",msg);
								}
								/** if the planet doesnt belong to the fleet user **/
								else {
									/** Anti-Hack (exploited by Pain & co)
									* Check again if planet is no a main planet
									* Also explioted using a fake haven form, such 
									* that an invasion to an illegal target could be launched **/
									if ((int)checkRow["planet_user_main"]==0) {
										this->pointsDef = 0;
										this->pointsAtt = 0;
		
										/** Load data of the planet user **/
										query << "SELECT ";
										query << "	user_points ";
										query << "FROM ";
										query << "	users ";
										query << "WHERE ";
										query << "	user_id='" << this->userToId << "';";
										mysqlpp::Result pointsDefRes = query.store();
										query.reset();
								
										if (pointsDefRes) {
											int pointsDefSize = pointsDefRes.size();
											
											if (pointsDefSize > 0) {
												mysqlpp::Row pointsDefRow = pointsDefRes.at(0);
												this->pointsDef = (int)pointsDefRow["user_points"];
											}
										}
								
										/** Load data of the fleet user **/
										query << "SELECT ";
										query << "	user_points ";
										query << "FROM ";
										query << "	users ";
										query << "WHERE ";
										query << "	user_id='" << fleet_["user_id"] << "';";
										mysqlpp::Result pointsAttRes = query.store();
										query.reset();
								
										if (pointsAttRes) {
											int pointsAttSize = pointsAttRes.size();
									
											if (pointsAttSize > 0) {
												mysqlpp::Row pointsAttRow = pointsAttRes.at(0);
												this->pointsAtt = (int)pointsAttRow["user_points"];
											}
										}
		
										/** Calculate the Chance **/
										this->chance = config.nget("INVADE_POSSIBILITY",0) / this->pointsAtt * this->pointsDef;
		
										/** Check if the chance is wheter higher then the max not lower then the min **/
										if(this->chance > config.nget("INVADE_POSSIBILITY",1))
											this->chance = config.nget("INVADE_POSSIBILITY",1);
										else if(this->chance < config.nget("INVADE_POSSIBILITY",1))
											this->chance = config.nget("INVADE_POSSIBILITY",1);
											
										this->one = rand() % 101;
										this->two = (100 * this->chance);
										
										if (one<=two) {
											bh->returnFleet = false;
											
											/** Load planet user count planets **/
											query << "SELECT ";
											query << "	COUNT(planet_user_id) as cnt ";
											query << "FROM ";
											query << "	planets ";
											query << "WHERE ";
											query << "	planet_user_id='" << fleet_["user_id"] << "';";
											mysqlpp::Result maxPlanetRes = query.store();
											query.reset();
									
											if (maxPlanetRes) {
												int maxPlanetSize = maxPlanetRes.size();
										
												if (maxPlanetSize > 0) {
													mysqlpp::Row maxPlanetRow = maxPlanetRes.at(0);
													
													/** if the user has already the number of planets **/
													if((int)maxPlanetRow["cnt"] < (int)config.nget("user_max_planets",0)) {
														/** Load the main planet of the victim **/
														query << "SELECT ";
														query << "	id ";
														query << "FROM ";
														query << "	planets ";
														query << "WHERE ";
														query << "	planet_user_id='" << this->userToId << "' ";
														query << "	AND planet_user_main='1';";
														mysqlpp::Result mPlanetRes = query.store();
														query.reset();
												
														if (mPlanetRes) {
															int mPlanetSize = mPlanetRes.size();
													
															if (mPlanetSize > 0) {
																mysqlpp::Row mPlanetRow = mPlanetRes.at(0);
																
																/** Send every fleet from the victim to his main planet **/
																query << "SELECT ";
																query << "	landtime, ";
																query << "	launchtime, ";
																query << "	entity_to, ";
																query << "	status, ";
																query << "	id ";
																query << "FROM ";
																query << "	fleet ";
																query << "WHERE ";
																query << "	user_id='" << this->userToId << "' ";
																query << "	AND entity_to='" << fleet_["entity_to"] << "';";
																mysqlpp::Result iflRes = query.store();
																query.reset();
														
																if (iflRes) {
																	int iflSize = iflRes.size();
																	mysqlpp::Row iflRow;
															
																	if (iflSize > 0) {
																		for (mysqlpp::Row::size_type i = 0; i<iflSize; i++)  {
																			iflRow = iflRes.at(i);
																			
																			this->duration = std::min((int)this->time,(int)iflRow["landtime"]) - (int)iflRow["fleet_launchtime"];
																			this->launchtime = this->time;
																			this->landtime = this->launchtime + this->duration;
																			
																			query << "UPDATE ";
																			query << "	fleet ";
																			query << "SET ";
																			query << "	entity_from='" << iflRow["entity_to"] << "', ";
																			query << "	entity_to='" << mPlanetRow["id"] << "', ";
																			query << "	status='2', ";
																			query << "	launchtime='" << this->launchtime << "', ";
																			query << "	landtime='" << this->landtime << "' ";
																			query << "WHERE ";
																			query << "	id='" << iflRow["id"] << "';";
																			query.store();
																			query.reset();
																		}
																		/** Send a message to the victim **/
																		std::string text = "Eure Schife, welche zum Planeten [b]";
																		text += coordsTarget;
																		text += "[/b] unterwegs waren, wurden auf euren Hauptplaneten umgeleitet!\n";
																		functions::sendMsg(this->userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Schiffe umgeleitet",text);
																	}
																}
															}
														}
													
														/** Invade the planet **/
														functions::invasionPlanet((int)fleet_["entity_to"],(int)fleet_["user_id"]); //ToDO
													
														/** Land fleet **/
														fleetLand(1);
													
														/** Create a message for the victim and the agressor **/
														std::string msg = msgAllShips;
														msg += msgRes;
													
														std::string text = "[b]Planet:[/b] ";
														text += coordsTarget;
														text += "\n[b]Besitzer:[/b] ";
														text += functions::getUserNick(this->userToId);
														text += "\n\nDieser Planet wurde von einer Flotte, welche vom Planeten ";
														text += coordsFrom;
														text += " stammt, übernommen!\n";
														functions::sendMsg(this->userToId,(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Kolonie wurde invasiert",text);
														text += "Ein Invasionsschiff wurde bei der Übernahme aufgebraucht!\n";
														text += msg;
														functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Planet erfolgreich invasiert",text);
														
														//Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo		
													}
													/** if the user has already reached the max number of planets **/
													else {
														bh->returnFleet = true;
														std::string text = "[b]Planet:[/b] ";
														text += coordsTarget;
														text += "\n[b]Besitzer:[/b] ";
														text += functions::getUserNick(this->userToId);
														text += "\n\nEine Flotte vom Planeten ";
														text += coordsFrom;
														text += " versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!";
														std::string text1 = "[b]Planet:[/b] ";
														text1 += coordsTarget;
														text1 += "\n[b]Besitzer:[/b] ";
														text1 += functions::getUserNick(this->userToId);
														text1 += "\n\nEine Flotte vom Planeten  ";
														text1 += coordsFrom;
														text1 += " versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg! Hinweis: Du hast bereits die maximale Anzahl Planeten erreicht!";
														
														functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Invasionsversuch gescheitert",text1);
														functions::sendMsg(this->userToId,(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Invasionsversuch gescheitert",text);
													}
												}
											}
										
											/** if the invasion failed **/
											else
											{
												std::string text = "[b]Planet:[/b] ";
												text += coordsTarget;
												text += "\n[b]Besitzer:[/b] ";
												text += functions::getUserNick(this->userToId);
												text += "\n\nEine Flotte vom Planeten ";
												text += coordsFrom;
												text += " versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!";
		
												functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Invasionsversuch gescheitert",text);
												functions::sendMsg(this->userToId,(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Invasionsversuch gescheitert",text);
											}
										}
				
									}
									
									/** if the planet is a main planet **/
									else {
										std::string text = "[b]Planet:[/b] ";
										text += coordsTarget;
										text += "\n[b]Besitzer:[/b] ";
										text += functions::getUserNick(this->userToId);
										text += "\n\nEine Flotte vom Planeten ";
										text += coordsFrom;
										text += " versuchte, das Ziel zu übernehmen. Dies ist aber ein Hauptplanet, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
								
										functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Invasionsversuch gescheitert",text);
										functions::sendMsg(this->userToId,(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Invasionsversuch gescheitert",text);
									}
								}
							}
						}
					}
					/** If no ship with the action was in the fleet **/
					else {
						std::string text = "Eine Flotte vom Planeten ";
						text += coordsFrom;
						text += " versuchte das Ziel zu übernehmen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
						functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Invasionsversuch gescheitert",text);
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
