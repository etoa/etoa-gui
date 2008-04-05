#include <iostream>

#include <mysql++/mysql++.h>

#include "BombardHandler.h"
#include "../../MysqlHandler.H"
#include "../../functions/Functions.h"

namespace bombard
{
	void BombardHandler::update()
	{
	
		/**
		* Fleet-Action: Bombard
		*/

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
			fleetLoadspecial(); //ToDo
		
			std::string coordsTarget = functions::formatCoords(fleet_["fleet_target_to"]);
			std::string coordsFrom = functions::formatCoords(fleet_["fleet_planet_from"]);
			
			int tLevel = 0;
		
			//Lädt Bombentechlevel
			mysqlpp::Query query = con_->query();
			query << "SELECT "; 
			query << "	techlist_current_level ";
			query << "FROM ";
			query << "	techlist ";
			query << "WHERE ";
			query << "	techlist_user_id='" << fleet_["fleet_user_id"] << "' ";
			query << "	AND techlist_tech_id='" << BOMB_TECH_ID << "'";
			mysqlpp::Result tRes = query.store();
			query.reset();
			
			if (tRes)
			{
				int tSize = tRes.size();
				
				if (tSize > 0)
				{
					mysqlpp::Row tRow tRes.at(0);
					tLevel = (int)tRow["techlist_current_level"];
				}
			}
				 
	
			// 10% + Bonis, dass Bombardierung erfolgreich
			double goOrNot=mt_rand(0,100); //ToDo
			if (goOrNot<=(10+(SHIP_BOMB_FACTOR*tLevel+$special_ship_bonus_build_destroy*100))) //ToDo
			{
				// Wählt EIN gebäude aus, welches nicht im bau ist
				query << "SELECT ";
				query << "	buildlist_id, ";
				query << "	buildlist_building_id, ";
				query << "	buildlist_current_level ";
				query << "FROM ";
				query << "	buildlist ";
				query << "WHERE ";
				query << "	buildlist_planet_id='" << fleet_["fleet_target_to"] << "' ";
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
						int bLevel = (int)blRow["buildlist_current_level"]-1;
	                
						//Lädt Gebäudenamen
						query << "SELECT ";
						query << "	building_name ";
						query << "FROM ";
						query << "	buildings ";
						query << "WHERE ";
						query << "building_id='" << blRow["buildlist_building_id"] << "'";
						mysqlpp::Res bRes = query.store();
						query.store();
						
						if (bRes)
						{
							int bSize = bRes.size();
							
							if (bSize > 0)
							{
								myslpp::Row bRow = bRes.at(0);
	                
								//Setzt Gebäude um ein Level zurück
								query << "UPDATE ";
								query << "	buildlist ";
								query << "SET ";
								query << "buildlist_current_level='" << bLevel << "' ";
								quer< << "WHERE ";
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
										query << "	fs_fleet_id='" << fleet_["fleet_id"] << "' ";
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
								query << "	fs_fleet_id='" << fleet_["fleet_id"] << "';";
								mysqlpp::Result checkRes = query.store();
								query.reset();
								
								if (checkRes)
								{
									int checkSize = checkRes.size();
									
									if (checkSize > 0)
									{
										mysqlpp:Row checkRow = checkRes.at(0);
										
										if (checkRow["cnt"]<=0)
										{
											returnFleet=false;
										}
									}
								}
	                
								//Nachricht senden
								text = "Eine Flotte vom Planet ";
								text += coordsFrom;
								text += " hat das Gebäude ";
								text += std::string(bRow["building_name"];
								text += " des Planeten ";
								text += coordsTarget;
								text += " um ein Level auf Stufe ";
								text += blRow["buildlist_current_level"];
								text += " zurück gesetzt";
								functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Gebäude bombardiert",text);
								fucntions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Gebäude bombardiert",text);
	                
								Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
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
				fucntions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Bombardierung gescheitert",text);
				functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Bombardierung gescheitert",text);
			}
		}

		if (returnFleet || returnV==4)
		{
			fleetReturn("br");
		}
		else
		{
			fleetDelete();
		}
	}
}

