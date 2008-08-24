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
		/*Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		std::string action = "bombard";

		// Calc battle
		BattleHandler *bh = new BattleHandler(con_,fleet_);
		bh->battle();

		// Send messages
		int userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		std::string subject1 = "Kampfbericht (";
		subject1 += bstat;
		subject1 += ")";
		std::string subject2 = "Kampfbericht (";
		subject2 += bstat2;
		subject2 += ")";
		functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),subject1,bh->msg);
		functions::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),subject2,bh->msg);

		// Add log
		functions::addLog(1,bh->msg,(int)fleet_["landtime"]);

		// Aktion durchführen
		if (bh->returnV==1)
		{
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
			query << "	AND (";
			query << "		ship_actions LIKE '%," << action << "'";
			query << "		OR ship_actions LIKE '" << action << ",%'";
			query << "		OR ship_actions LIKE '%," << action << ",%'";
			query << "		OR ship_actions LIKE '" << action << "');";
			mysqlpp::Result fsRes = query.store();
			query.reset();
		
					
			if (fsRes)
			{
				int fsSize = fsRes.size();
				
				if (fsSize > 0)
				{
					mysqlpp::Row fsRow = fsRes.at(0);
					
					int shipCnt = fsRow["cnt"];
					
					if (shipCnt > 0)
					{
		
						std::string coordsTarget = functions::formatCoords(fleet_["entity_to"],0);
						std::string coordsFrom = functions::formatCoords(fleet_["entity_from"],0);
			
						int tLevel = 0;
		
						//Lädt Bombentechlevel
						query << "SELECT "; 
						query << "	techlist_current_level ";
						query << "FROM ";
						query << "	techlist ";
						query << "WHERE ";
						query << "	techlist_user_id='" << fleet_["user_id"] << "' ";
						query << "	AND techlist_tech_id='" << config.idget("BOMB_TECH_ID") << "'";
						mysqlpp::Result tRes = query.store();
						query.reset();
			
						if (tRes)
						{
							int tSize = tRes.size();
				
							if (tSize > 0)
							{
								mysqlpp::Row tRow = tRes.at(0);
								tLevel = (int)tRow["techlist_current_level"];
							}
						}
				 
	
						// 10% + Bonis, dass Bombardierung erfolgreich
						int goOrNot = rand() % 101;
			
						if (goOrNot <= (config.nget("ship_bomb_factor",1) + (config.nget("ship_bomb_factor",0) * tLevel shipCnt / 10000 + bh->specialShipBonusBuildDestroy * 100)))
						{
							// Wählt EIN gebäude aus, welches nicht im bau ist
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
				
							if (blRes)
							{
								int blSize = blRes.size();
					
								if (blSize > 0)
								{
									mysqlpp::Row blRow = blRes.at(0);
						
									//Gebäude ein Stuffe zurücksetzten
									int bLevel = (int)blRow["buildlist_current_level"] - ceil(shipCnt/2500);
	                
									//Lädt Gebäudenamen
									query << "SELECT ";
									query << "	building_name ";
									query << "FROM ";
									query << "	buildings ";
									query << "WHERE ";
									query << "building_id='" << blRow["buildlist_building_id"] << "';";
									mysqlpp::Result bRes = query.store();
									query.store();
						
									if (bRes)
									{
										int bSize = bRes.size();
							
										if (bSize > 0)
										{
											mysqlpp::Row bRow = bRes.at(0);
	                
											//Setzt Gebäude um ein Level zurück
											query << "UPDATE ";
											query << "	buildlist ";
											query << "SET ";
											query << "buildlist_current_level='" << bLevel << "' ";
											query << "WHERE ";
											query << "buildlist_id=" << blRow["buildlist_id"] << "";
											query.store();
											query.reset();
								
											//Zieht 1 Bomberschiff von der Flotte ab
											query << "SELECT ";
											query << "	ship_id ";
											query << "FROM ";
											query << "	ships ";
											query << "WHERE ";
											query << "ship_build_destroy='1'";
											mysqlpp::Result sRes = query.store();
											query.reset();
								
											if (sRes)
											{
												int sSize = sRes.size();
									
												if (sSize > 0)
												{
													mysqlpp::Row sRow = sRes.at(0);
											                
													query << "UPDATE ";
													query << "	fleet_ships ";
													query << "SET ";
													query << "	fs_ship_cnt=fs_ship_cnt-1 ";
													query << "WHERE ";
													query << "	fs_fleet_id='" << fleet_["id"] << "' ";
													query << "	AND fs_ship_id='" << sRow["ship_id"] << "';";
													query.store();
													query.reset();
												}
											}
							
											//Wenn kein Schiff mehr in der flotte ist, kein rückflug
											query << "SELECT ";
											query << "	SUM(fs_ship_cnt) AS cnt ";
											query << "FROM ";
											query << "	fleet_ships ";
											query << "WHERE ";
											query << "	fs_fleet_id='" << fleet_["id"] << "';";
											mysqlpp::Result checkRes = query.store();
											query.reset();
								
											if (checkRes)
											{
												int checkSize = checkRes.size();
									
												if (checkSize > 0)
												{
													mysqlpp::Row checkRow = checkRes.at(0);
										
													if ((int)checkRow["cnt"]<=0)
													{
														bh->returnFleet = false;
													}
												}
											}
	                
											//Nachricht senden
											text = "Eine Flotte vom Planet ";
											text += coordsFrom;
											text += " hat das Gebäude ";
											text += std::string(bRow["building_name"]);
											text += " des Planeten ";
											text += coordsTarget;
											text += " um ein Level auf Stufe ";
											text += std::string(blRow["buildlist_current_level"]);
											text += " zurück gesetzt";
											functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Gebäude bombardiert",text);
											functions::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Gebäude bombardiert",text);
	                
											//Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
										}
									}
								}
							}
						}
						else
						{
							text = "Eine Flotte vom Planet ";
							text += coordsFrom;
							text += " hat erfolglos versucht ein Gebäude des Planeten ";
							text += coordsTarget;
							text += " um ein Level zu senken";
							functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Bombardierung gescheitert",text);
							functions::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Bombardierung gescheitert",text);
						}
					}
				}
			}
		}
		else
		{
			std::string text = "Eine Flotte vom Planeten ";
			text += functions::formatCoords((int)fleet_["entity_from"],0);
			text += " versuchte, eine Kolonie zu errichten. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
			functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Bombardierung gescheitert",text);
				
			fleetReturn(1);
		}

		if (bh->returnFleet || bh->returnV==4)
		{
			fleetReturn(1);
		}
		else
		{
			fleetDelete();
		}*/
	}
}
