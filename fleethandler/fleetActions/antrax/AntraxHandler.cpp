#include <iostream>

#include <mysql++/mysql++.h>

#include "AntraxHandler.h"
#include "../../MysqlHandler.H"
#include "../../functions/Functions.h"

namespace antrax
{
	void AntraxHandler::update()
	{
	
		/**
		* Fleet-Action: Antrax-Attack
		*/

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
			
			int tLevel = 0;
			
			//Lädt Gifttechnologie level
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	techlist_current_level ";
			query << "FROM ";
			query << "	techlist ";
			query << "WHERE ";
			query << "	techlist_user_id='" << fleet_["'fleet_user_id"] << "' ";
			query << "	AND techlist_tech_id='18';";
			mysqlpp::Result tRes = query.store();
			query.reset();
			
			if (tRes)
			{
				int tSize = tRes.size();
				
				if (tSize > 0)
				{
					mysqlpp::Row tRow = Res.at(0),
					
					tLevel = (int)tRow["techlist_current_level"];
				}
			}
		
			//40% + Boni Chance, dass Antrax erflogreich
			double goOrNot=mt_rand(0,100); //ToDo
			if (goOrNor<=(40+tLevel*5+$special_ship_bonus_antrax_food*100)) //ToDo
			{
				std::string coordsTarget = functions::formatCoords(fleet_["fleet_target_to"]);
				std::string coordsFrom = functions::formatCoords(fleet_["fleet_target_from"]);
				
				//Rechnet Schadensfaktor (Max. 90%)
				double fak = mt_rand(1,min((10+tLevel*3),90)); //ToDo
				
				//Lädt Nahrung und Bewohner
				query << "SELECT ";
				query << "	planet_res_food, ";
				query << "	planet_people ";
				query << "FROM ";
				query << "	planets ";
				query << "WHERE ";
				query << "	id='" << fleet_["fleet_target_to"] << "';";
				mysqlpp::Result pRes = query.store();
				query.reset();
				
				if (pRes)
				{
					int pSize = pRes.size();
					
					if (pSize > 0)
					{
						mysqlpp::Row pRow = pRes.at(0);
				
						//Rechnet Bewohner und Nahrungsverluste
						double people = round(pRow["planet_people"] * fak / 100);
						double peopleRest = pRow["planet_people"] - people;
						double food = round(pRow["planet_res_food"] * fak / 100);
						double foodRest = pRow["planet_res_food"] - food;
				
						//Zieht Nahrung und Bewohner vom Planeten ab
						query << "UPDATE ";
						query << "	planets ";
						query << "SET ";
						query << "	planet_res_food='" << foodRest << "', ";
						query << "	planet_people='" << peopleRest << "' ";
						query << "WHERE ";
						query << "	id='" << fleet_["fleet_target_to"] << "';";
						query.store();
						query.reset();
				
						//Nachricht senden
						std::string text = "Eine Flotte vom Planet ";
						text += coordsFrom;
						text += " hat einen Antraxangriff auf den Planeten ";
						text += coordsTarget;
						text += " verübt es starben dabei ";
						text += functions::nf(people);
						text += " Bewohner und ";
						text += functions::nf(food);
						text += " t Nahrung wurden dabei verbrannt.";
						
						functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Antraxangriff"$text);
						functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Antraxangriff",text);
						
						Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
					}
				}
			} 
			else 
			{
				//Nachricht senden (Antraxangriff fehlgeschlagen)
				std::string text = "Eine Flotte vom Planet ";
				text += coordsFrom;
				text += " hat erfolglos einen Antraxangriff auf den Planeten ";
				text += coordsTarget;
				text += " verübt.";
				functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Antraxangriff erfolglos",text);
				functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Antraxangriff erfolglos",text);
			}
		}
	
		if (returnFleet || returnV==4)
		{
			fleetReturn("hr");
		}
		else
		{
			fleetDelete();
		}
	}
}

