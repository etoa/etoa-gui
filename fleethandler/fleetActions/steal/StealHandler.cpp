#include <iostream>

#include <mysql++/mysql++.h>

#include "StealHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"

namespace steal
{
	void StealHandler::update()
	{
	
		/**
		* Fleet-Action: Spy-Attack (Steal technology)
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

			coordsTarget = functions::formatCoords((int)fleet_["fleet_target_to"]);
			coordsFrom = functions::formatCoords((int)fleet_["fleet_target_from"]);
		
			int tLevelAtt = 0, tLevelDef;

			//Lädt Spiotech level der Kontrahenten
			//Angreiffer
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	techlist_current_level ";
			query << "FROM ";
			query << "	techlist ";
			query << "WHERE ";
			query << "	techlist_user_id='" << fleet_["fleet_user_id"] << "' ";
			query << "	AND techlist_tech_id='7'";
			mysqlpp::Result tRes = query.store();
			query.reset();
			
			if (tRes)
			{
				int tSize = tRes.size();
				
				if (tSize > 0)
				{
					mysqlpp::Row tRow = tRes.at(0),
					tLevelAtt = (int)tRow["techlist_current_level"];
				}
			}	
            	
			//Verteidiger
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	techlist_current_level ";
			query << "FROM ";
			query << "	techlist ";
			query << "WHERE ";
			query << "	techlist_user_id='" << userToId << "' ";
			query << "	AND techlist_tech_id='7'";
			mysqlpp::Result tRes = query.store();
			query.reset();
			
			if (tRes)
			{
				int tSize = tRes.size();
				
				if (tSize > 0)
				{
					mysqlpp::Row tRow = tRes.at(0),
					tLevelDef = (int)tRow["techlist_current_level"];
				}
			}

			//3% + (Spiotech Angreiffer - Spiotech Verteidiger) + Boni Chance, dass Spioangriff erfolgreich
			double goOrNot = mt_rand(1,100); //ToDo
			double chance = 3 + (tLevelAtt - tLevelDef + $special_ship_bonus_forsteal*100); //ToDo
			if (goOrNot<=chance && chance>0)
			{
				//Sucht eine zufalls Tech vom gegner aus, welche einen höheren Level als die eigenen techs haben. Es werden nur tech geladen, welche man selber schon einmal geforscht hat und die tech, die man selber grad forscht wird ausgeschlossen!
				// PATCH:  AND att.techlist_current_level>0 behebt einen Bug dass angeforschte und abgebrochene techs auch genommen werden
				// PATCH2: AND t.tech_stealable = '1' macht es möglich, das manche Forschungen gar nie abgeschaut werden können (z.B Gentech)
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
                query << "			AND att.techlist_build_type!=1 ";
                query << "			AND def.techlist_user_id='" << userToId << "' ";
				query << "			AND att.techlist_user_id=" < fleet_["fleet_user_id"] << " ";
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
				
				if (techRes)
				{
					int techSize = techRes.size();
					
					if (techSize > 0)
					{
						mysqlpp::Row techRow = techRes.at(0);
						
						//Beendet die eigene Forschung, falls ihr Ausbau über die maximal Stufe rausragen würde
						if(techRow["def_techlist_current_level"]==techRow["tech_last_level"])
						{
							query << "UPDATE ";
							query << "	techlist ";
							query << "SET ";
							query << "	techlist_current_level='" << techRow["def_techlist_current_level"] << "', ";
							query << "	techlist_build_type='0', ";
							query << "	techlist_build_start_time='0', ";
							query << "	techlist_build_end_time='0' ";
							query << "WHERE ";
							query << "	techlist_user_id=" << fleet_["fleet_user_id"] << " ";
							query << "	AND techlist_tech_id=" << techRow["techlist_tech_id"] << ";";
							query.store();
							query.reset();
						}
						else
						{
							query << "UPDATE ";
							query << "	techlist ";
							query << "SET ";
							query << "	techlist_current_level=" << techRow["def_techlist_current_level"] << " ";
							query << "WHERE ";
							query << "	techlist_user_id=" << fleet_["fleet_user_id"] << " ";
							query << "	AND techlist_tech_id=" << techRow["techlist_tech_id"] << ";";
							query.store();
							query.reset();
						}

						//Zieht 1 Tech-Klau Schiff von der Flotte ab
						query << "SELECT ";
						query << "	ship_id ";
						query << "FROM ";
						query << "	ships ";
						query << "WHERE ";
						query << "	ship_forsteal='1';";
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
								query << "	AND fs_ship_id='" << sRow["ship_id"] < "';";
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
								mysqlpp::Row checkRow = checkRes.at(0);
          
								if ((int)checkRow["cnt"]<=0)
								{
									//Flotte löschen
									returnFleet=false;
								}
							}
						}
                    
						//Nachricht senden
						std::string text = "Eine Flotte vom Planeten ";
						text += coordsFrom;
						text += " hat erfolgreich einen Spionageangriff durchgeführt und erfuhr so die Geheimnisse der Forschung ";
						text += std::string(techRow["tech_name"]);
						text += " bis zum Level ";
						text += std::string(techRow["def_techlist_current_level"]);
						text += "\n";
						
						functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Spionageangriff",text);
						functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Spionageangriff",text);
						
						functions::addLog(FLEET_ACTION_LOG_CAT,text,(int)fleet_["fleet_landtime"]);
                    
						Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
                    
					}
					else
					{
						//Nachricht senden (kein Abschauen möglich)
						std::string text = "Eine Flotte vom Planeten ";
						text += coordsFrom;
						text += " hat erfolglos einen Spionageangriff durchgeführt.\n Das Ziel hat keine Technologie, welche eine höhere Stufe hat!";
						
						fucntions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",text);
						functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",text);

						functions::addLog(FLEET_ACTION_LOG_CAT,text,(int)fleet_["fleet_landtime"]);
					}
				}
			}
			else
			{
				//Nachricht senden (Spioangriff fehlgeschlagen)
				std::string text = "Eine Flotte vom Planeten ";
				text += coordsFrom;
				text += " hat erfolglos einen Spionageangriff durchgeführt.";
				
				functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",text);
				functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",text);

				functions::addLog(FLEET_ACTION_LOG_CAT,text,(int)fleet_["fleet_landtime"]);
			}
		}

		if (returnFleet || returnV==4)
		{
			fleetReturn("lr");
		}
		else
		{
			fleetDelete();
		}
	}
}
