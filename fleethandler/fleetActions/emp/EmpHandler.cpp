#include <iostream>

#include <mysql++/mysql++.h>

#include "EmpHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"

namespace emp
{
	void EmpHandler::update()
	{
	
		/**
		* Fleet-Action: EMP-Attack
		*/

		// Calc battle
		battle();

		// Send messages
		int userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		std::string subject1 = "Kampfbericht (";
		subject1 += bstat;
		subject1 += ")";
		std::string = subject2 = "Kampfbericht (";
		subject2 += bstat2;
		subject2 += ")";
		functions::sendMsg((int)fleet_["user_id"],SHIP_WAR_MSG_CAT_ID,subject1,msgFight);
		functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,subject2,msgFight);

		// Add log
		functions::addLog(1,msgFight,(int)fleet_["landtime"]);

		// Aktion durchführen
		if (returnV==1)
		{
			returnFleet = true;
			fleetLoadspecial(); //ToDo

			coordsTarget = functions::formatCoords((int)fleet_["entity_to"]);
			coordsFrom = functions::formatCoords((int)fleet_["entity_from"]);
		
			int tLevel = 0;
			
			//Lädt EMP-Tech level
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	techlist_current_level ";
			query << "FROM ";
			query << "	techlist ";
			query << "WHERE ";
			query << "	techlist_user_id='" << fleet_["user_id"] << "' ";
			query << "	AND techlist_tech_id='17'";
			mysqlpp::Result tRes = query.store();
			query.reset();
			
			if (tRes)
			{
				int tSize = tRes.size();
				
				if (tSize > 0)
				{
					mysqlpp::Row tRow = tRes.at(0),
					tLevel = (int)tRow["techlist_current_level"];
				}
			}
		
			//10% + Bonis, dass Deaktivierung erfolgreich
			double goOrNot=mt_rand(0,100); //ToDo
			if (goOrNot<(10+(SHIP_BOMB_FACTOR*tLevel+$special_ship_bonus_deactivade*100))) //ToDo
			{
				//Generiert Zufallswert, wie viele Stunden das Gebäude deaktiviert wird (min. 1h)
				double percent = mt_rand(1,(10+tLevel)); //ToDo
				double plus = percent*3600;
				int h = floor(plus/3600);
				if (tLevel==0)
				{
					tLevel = 1;
				}

				//Lädt Zufällig Schiffswerft, Waffenfabrik, Flottenkontrolle, Raketensilo oder Kryptocenter
				query << "SELECT ";
				query << "	buildlist_deactivated, ";
				query << "	buildlist_building_id ";
				query << "FROM ";
				query << "buildlist ";
				query << "WHERE ";
				query << "	buildlist_planet_id='" << fleet_["entity_to"] << "' ";
				query << "	AND buildlist_current_level > 0 ";
				query << "	AND (";
				query << "		buildlist_building_id='" << FLEET_CONTROL_ID << "' ";
				query << "		OR buildlist_building_id='" << FACTORY_ID << "' ";
				query << "		OR buildlist_building_id='" << SHIPYARD_ID << "' ";
				query << "		OR buildlist_building_id='" << BUILD_MISSILE_ID << "' ";
				query << "		OR buildlist_building_id='" << BUILD_CRYPTO_ID << "' ";
				query << "		)";
				query << "ORDER BY ";
				query << "	RAND() ";
				query << "LIMIT 1;";
				mysqlpp::Result bRes = query.store(),
				query.reset();
				
				if (bRes)
				{
					int bSize = bRes.size();
					
					if (bSize > 0)
					{
						mysqlppRow bRow = bRes.at(0);

						//Rechnet die Deaktivierungszeit (summiert Zeit)
						int time = max((int)fleet-["landtime"],(int)bRow["buildlist_deactivated"]);
						int time2Add = time + plus;

						//Deaktivierzeit Updaten
						query << "UPDATE ";
						query << "	buildlist ";
						query << "SET ";
						query << "	buildlist_deactivated='" << time2add << "' ";
						query << "WHERE ";
						query << "	buildlist_planet_id='" << fleet_["entity_to"] << "' ";
						query << "	AND buildlist_building_id='" << bRow["buildlist_building_id"] << "'";
						query.store();
						query.reset();

						//Lädt Gebäudename
						query << "SELECT ";
						query << "	building_name ";
						query << "FROM ";
						query << "	buildings ";
						query << "WHERE ";
						query << "	building_id='" << bRow["buildlist_building_id"] << "';";
						mysqlpp::Result nameRes = query.store();
						query.reset();
					
						if (nameRes)
						{
							int nameSize = nameRes.size();
					 
							if (nameSize > 0)
							{
								mysqlpp::Row nameRow = nameRes.at(0);

								//Zieht 1 Deaktivierungsbomber von der Flotte ab
								query << "SELECT ";
								query << "	ship_id ";
								query << "FROM ";
								query << "	ships ";
								query << "WHERE ";
								query << "	ship_deactivade='1'";//ToD
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
								text += " das Gebäude ";
								text += blRow["buildlist_current_level"];
								text += " für ";
								text += h;
								text += "h deaktiviert.";
								functions::sendMsg((int)fleet_["user_id"],SHIP_WAR_MSG_CAT_ID,"GebäDeaktivierung",text);
								fucntions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Deaktivierung",text);
	                
								//Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
							}
						}
					}
	            }
				else
				{
					//Nachricht senden (Es wurden noch keine Gebäude gebaut, welche deaktiviert werden können)
					text = "Eine Flotte vom Planet ";
					text += coordsFrom;
					text += " hat erfolglos versucht auf dem Planeten ";
					text += coordsTarget;
					text += " ein Gebäude zu deaktivieren.\nHinweis: Der Spieler hat keine Gebäudeeinrichtungen, welche deaktiviert werden können!";
					fucntions::sendMsg((int)fleet_["user_id"],SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",text);
					functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",text);
				}
			}
			else
			{
				//Nachricht senden (Deaktivierung fehlgeschlagen)
				text = "Eine Flotte vom Planet ";
				text += coordsFrom;
				text += " hat erfolglos versucht auf dem Planeten ";
				text += coords_target;
				text += " ein Gebäude zu deaktivieren.";
				functions::sendMsg((int)fleet_["user_id"],SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",text);
				functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",text);
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
