#include <iostream>

#include <mysql++/mysql++.h>

#include "InvadeHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"

namespace invade
{
	void InvadeHandler::update()
	{
	
		/**
		* Fleet-Action: Invade
		*/
		Config &config = Config::instance(); //ToDo time init;
	
		//Lädt User-ID des momentanen Besitzers
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	planet_user_id, ";
		query << "	planet_user_main ";
		query << "FROM ";
		query << "	planets ";
		query << "WHERE ";
		query << "	id='" << fleet_["fleet_target_to"] << "'";
		mysqlpp::Result checkRes = query.store();
		query.reset();
		
		if (checkRes)
		{
			int checkSize = checkRes.size();
			
			if (checkSize > 0)
			{
				mysqlpp::Row checkRow = checkRes.at(0);
				
				//Kontrolliert bei einer Invasion, ob der Planet nicht schon demjenigengehört gehört
				//gehört bereits dem User, dann flotte stationieren
				if(checkRow["planet_user_id"]==fleet_["fleet_user_id"])
				{
					//Flotte stationieren & Waren ausladen (ohne den Abzug eines Invasionsschiffes)
					fleetLand(1,0,1);

					// Flottelöschen
					fleetDelete();

				// Nachricht senden
				std::string msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ";
				msg += functions::formatCoords((int)fleet_["fleet_target_to"]);
				msg += "\n[b]Zeit:[/b] ";
				msg += functions::formatTime((int)fleet_["fleet_landtime"]);
				msg += "\n[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!";
				msg += msgAllShips;
				msg += msgRes;
				functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_MISC_MSG_CAT_ID,"Flotte angekommen",msg);
			}
			//gehört nicht dem User, dann fight
			else
			{
				// Calc battle
				battle();
	
				// Send messages
				int userToId = functions::getUserIdByPlanet((int)fleet_["fleet_target_to"]);
				std::string subject1 = "Kampfbericht (";
				subject1 += bstat;
				subject1 += ")";
				std::string = subject2 = "Kampfbericht (";
				subject2 += bstat2;
				subject2 += ")";
				functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,subject1,msgFight);
				functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,subject2,msgFight);

				// Add log
				functions::addLog(1,msgFight,(int)fleet_["fleet_landtime"]);

				// Aktion durchführen
				if (returnV==1)
				{
					returnFleet = true;
			
					// Anti-Hack (exploited by Pain & co)
					// Check if an invasion ship is part of the fleet (exploited by faking the form which calls fleet_launch,
					// setting an fleet action which wasn't allowed)
					// Attention: The invasion ship could break in battle, this doesn't matter in the past, but now it will matter..
					// This issue has to be discussed, perhabs this check should be performed before the battle
					query << "SELECT ";
					query << "	ship_id ";
					query << "FROM ";
					query << "	fleet_ships ";
					query << "INNER JOIN ";
					query << "	ships ON fs_ship_id = ship_id ";
					query << "	AND fs_fleet_id='" << fleet_["fleet_id"] << "' ";
					query << "	AND fs_ship_faked='0' ";
					query << "	AND ship_invade=1;";
					mysqlpp::Result fsRes = query.store();
					query.reset();
					
					if (fsRes)
					{
						int fsSize = fsRes.size();
						
						if (fsSize > 0)
						{
							
							// Anti-Hack (exploited by Pain & co)
							// Check again if planet is no a main planet
							// Also explioted using a fake haven form, such 
							// that an invasion to an illegal target could be launched
							if (checkRow["planet_user_main"]==0)
							{

								std::string coordsTarget = functions::formatCoords(fleet_["fleet_planet_to"]);
								std::string coordsFrom = functions::formatCoords(fleet_["fleet_planet_from"]);
								
								double pointsDef = 0, pointsAtt = 0;
		
								//Liest Punkte des 'Opfers' aus
								query << "SELECT ";
								query << "	user_points ";
								query << "FROM ";
								query << "	users ";
								query << "WHERE ";
								query << "	user_id='" << userToId << "';";
								mysqlpp::Result pointsDefRes = query.store();
								query.reset();
								
								if (pointsDefRes)
								{
									int pointsDefSize = pointsDefRes.size();
									
									if (pointsDefSize > 0)
									{
										mysqlpp::Row pointsDefRow = pointsDefRes.at(0);
										pointsDef = pointsDefRow["user_points"];
									}
								}
								
								//Liest Punkte des Angreiffers aus
								query << "SELECT ";
								query << "	user_points ";
								query << "FROM ";
								query << "	users ";
								query << "WHERE ";
								query << "	user_id='" << fleet_["fleet_user_id"] << "';";
								mysqlpp::Result pointsAttRes = query.store();
								query.reset();
								
								if (pointsAttRes)
								{
									int pointsAttSize = pointsAttRes.size();
									
									if (pointsAttSize > 0)
									{
										mysqlpp::Row pointsAttfRow = pointsAttRes.at(0);
										pointsAtt = pointsAttRow["user_points"];
									}
								}
		
								//Punkteverhältnis
								double chance = INVADE_POSSIBILITY / pointsAtt * pointsDef;
		
								//Prüft, ob das Verhältnis die Mindest- bzw. Maximalgrenze nicht unter- oder überschreitet
								if(factor<1 && chance>INVADE_MAX_POSSIBILITY) //factor?? ToDo
								{
									chance = INVADE_MAX_POSSIBILITY;
								}
								else if(factor>1 && chance<INVADE_MIN_POSSIBILITY)
								{
									chance = INVADE_MIN_POSSIBILITY;
								}
		
								double iposs = mt_rand(0,100); //ToDo
								double iperc = intval(100*chance); //ToDo
				
								//Ist invasion erfolgreich? (Chance ok)
								if (iposs<=iperc)
								{
									// Lade Planeten des Users
									query << "SELECT ";
									query << "	COUNT(planet_user_id) as cnt";
									query << "FROM ";
									query << "	planets ";
									query << "WHERE ";
									query << "	planet_user_id='" << fleet_["fleet_user_id"] << "';";
									mysqlpp::Result maxPlanetRes = query.store();
									query.reset();
									
									if (maxPlanetRes)
									{
										int maxPlanetSize = maxPlanetRes.size();
										
										if (maxPlanetSize > 0)
										{
											mysqlpp::Row maxPlanetRow = maxPlanetRes.at(0);
							
											//Hat der User schon die maximale Anzahl Planeten?
											if((int)maxPlanetRow["cnt"] < (int)config.nget("user_max_planets",0))
											{
												//Liest Planet ID und Cell ID vom HP des 'Opfers' aus
												query << "SELECT ";
												query << "	id, ";
												query << "FROM ";
												query << "	planets ";
												query << "WHERE ";
												query << "	planet_user_id='" << userToId << "' ";
												query << "	AND planet_user_main='1';";
												mysqlpp::Result mPlanetRes = query.store();
												query.reset();
												
												if (mPlanetRes)
												{
													int mPlanetSize = mPlanetRes.size();
													
													if (mPlanetSize > 0)
													{
														mysqlpp::Row mPlanetRow = mPlanetRes.at(0);
																
														// Alle Flotten des 'Opfers', zum Planeten fliegen zum Hauptplaneten schicken mit der Aktion 'Flug abgebrochen'
														query << "SELECT ";
														query << "	fleet_landtime, ";
														query << "	fleet_launchtime, ";
														query << "	fleet_target_to, ";
														query << "	fleet_action, ";
														query << "	fleet_id ";
														query << "FROM ";
														query << "	fleet ";
														query << "WHERE ";
														query << "	fleet_user_id='" << userToId << "' ";
														query << "	AND fleet_target_to='" << fleet_["fleet_target_to"] << "';";
														mysqlpp::Result iflRes = query.store();
														query.reset()
														
														if (iflRes)
														{
															int iflSize = iflRes.size();
															mysqlpp::Row iflRow;
															
															if (iflSize > 0)
															{
																for (mysqlpp::Row::size_type i = 0; i<iflSize; i++) 
																{
																	row = res.at(i);
	    			
																	int duration = min(time,(int)iflRow["fleet_landtime"]) - (int)iflRow["fleet_launchtime"];
																	int launchtime = time;
																	int landtime = launchtime + duration;
																    char str[4] = "";
																	strcpy( str, fleet_["fleet_action"]);
																	action = str[0];
																	action += "c";
																	
																	query << "UPDATE ";
																	query << "	fleet ";
																	query << "SET ";
																	query << "	fleet_target_from='" << iflRow["fleet_cell_to"] << "', ";
																	query << "	fleet_target_to='" << mPlanetRow["id"] << "', ";
																    query << "	fleet_action='" << action << "', ";
																	query << "	fleet_launchtime='" << launchtime << "', ";
																	query << "	fleet_landtime='" << landtime << "' ";
																	query << "WHERE ";
																	query << "	fleet_id='" << iflRow["fleet_id"] << "';";
																	query.store();
																	query.reset();
																}
																//
																std::string text = "Eure Schife, welche zum Planeten [b]";
																text += coordsTarget;
																text += "[/b] unterwegs waren, wurden auf euren Hauptplaneten umgeleitet!\n";
																functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Schiffe umgeleitet",text);
															}
														}
													}
												}
											}
		
											// Planet übernehmen
											functions::invasionPlanet((int)fleet_["fleet_target_to"],(int)fleet_["fleet_user_id"]); //ToDo
		
											//Flotte Stationieren & Waren ausladen
											fleetLand(1);
		
											//Gelandete Schiffe und Rohstoffe speichern
											std::string msg = msgAllShip;
											msg += msgRes;
		
											// Nachrichten senden
											std::string text = "[b]Planet:[/b] "+;
											text += coordsTarget;
											text += "\n[b]Besitzer:[/b] ";
											text += functions::getUserNick(userToId);
											text += "\n\nDieser Planet wurde von einer Flotte, welche vom Planeten ";
											text += coordsFrom;
											text += " stammt, übernommen!\n";
											text += "Ein Invasionsschiff wurde bei der Übernahme aufgebraucht!\n";
											text += msg;
											functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Planet erfolgreich invasiert",text);
											functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Kolonie wurde invasiert",text);
		
											Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
		
		
											returnFleet = false;
										}
										//Der User hat bereits die maximale Anzahl Planeten
										else
										{
											std::string text = "[b]Planet:[/b] ";
											text += coordsTarget;
											text += "\n[b]Besitzer:[/b] ";
											text += functions::getUserNick(userToId);
											text += "\n\nEine Flotte vom Planeten ";
											text += coordsFrom;
											text += " versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!";
											std::string text1 = "[b]Planet:[/b] ";
											text1 += coordsTarget;
											text1 += "\n[b]Besitzer:[/b] ";
											text1 += functions::getUserNick(userToId);
											text1 += "\n\nEine Flotte vom Planeten  ";
											text1 += coordsFrom;
											text1 += " versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg! Hinweis: Du hast bereits die maximale Anzahl Planeten erreicht!";
		
											functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",text1);
											functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",text);
										}
									}
								
								}
								// Invasion klappte nicht
								else
								{
									std::string text = "[b]Planet:[/b] ";
									text += coordsTarget;
									text += "\n[b]Besitzer:[/b] ";
									text += functions::getUserNick(userToId);
									text += "\n\nEine Flotte vom Planeten ";
									text += coordsFrom;
									text += " versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!";
		
									functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",text);
									functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",text);
								}
				
							}
							else
							{
								std::string text = "[b]Planet:[/b] ";
								text += coordsTarget;
								text += "\n[b]Besitzer:[/b] ";
								text += functions::getUserNick(userToId);
								text += "\n\nEine Flotte vom Planeten ";
								text += coordsFrom;
								text += " versuchte, das Ziel zu übernehmen. Dies ist aber ein Hauptplanet, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
								
								functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",text);
								functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",text);
							}
						}
						else
						{
							std::string text = "[b]Planet:[/b]";
							text += coordsTarget;
							text += "\n[b]Besitzer:[/b] ";
							text += functions::getUserNick(userToId);
							text += "\n\nEine Flotte vom Planeten ";
							text += coordsFrom;
							text += " versuchte, das Ziel zu übernehmen. Leider war kein Schiff mehr in der Flotte, welches einen Planeten invasieren kann, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
							functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",text);
							functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",text);
						}
					}
				}
			}
			if (returnFleet || returnV==4)
			{
				fleetReturn(1);
			}
			else
			{
				fleetDelete();
			}
		}
	}
}
