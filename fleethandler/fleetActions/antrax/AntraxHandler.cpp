#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "AntraxHandler.h"
#include "../../MysqlHandler.H"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

#include "../../battle/BattleHandler.h"
namespace antrax
{
	void AntraxHandler::update()
	{
	
		/**
		* Fleet-Action: Antrax-Attack
		*/
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);

		
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
			
			int tLevel = 0;
			
			//Lädt Gifttechnologie level
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	techlist_current_level ";
			query << "FROM ";
			query << "	techlist ";
			query << "WHERE ";
			query << "	techlist_user_id='" << fleet_["'user_id"] << "' ";
			query << "	AND techlist_tech_id='" << config.idget("Gifttechnologie") << "';";
			mysqlpp::Result tRes = query.store();
			query.reset();
			
			if (tRes)
			{
				int tSize = tRes.size();
				
				mysqlpp::Row tRow;
				
				if (tSize > 0)
				{
					tRow = tRes.at(0);
					
					tLevel = (double)tRow["techlist_current_level"];

				}
			}
		
			std::string coordsTarget = functions::formatCoords(fleet_["entity_to"],0);
			std::string coordsFrom = functions::formatCoords(fleet_["entity_from"],0);
				
			//40% + Boni Chance, dass Antrax erflogreich
			double goOrNot = rand() % 101;
			if (goOrNot <= (config.nget("antrax_action",0) + tLevel * 5 + bh->specialShipBonusAntrax * 100)) //ToDo
			{
				//Rechnet Schadensfaktor (Max. 90%)
				int temp = (int)std::min((10 + tLevel * 3),90);
				double fak = rand() % temp;
				
				//Lädt Nahrung und Bewohner
				query << "SELECT ";
				query << "	planet_res_food, ";
				query << "	planet_people ";
				query << "FROM ";
				query << "	planets ";
				query << "WHERE ";
				query << "	id='" << fleet_["entity_to"] << "';";
				mysqlpp::Result pRes = query.store();
				query.reset();
				
				if (pRes)
				{
					int pSize = pRes.size();
					
					if (pSize > 0)
					{
						mysqlpp::Row pRow = pRes.at(0);
				
						//Rechnet Bewohner und Nahrungsverluste
						double people = round((double)pRow["planet_people"] * fak / 100);
						double peopleRest = (double)pRow["planet_people"] - people;
						double food = round((double)pRow["planet_res_food"] * fak / 100);
						double foodRest = (double)pRow["planet_res_food"] - food;
				
						//Zieht Nahrung und Bewohner vom Planeten ab
						query << "UPDATE ";
						query << "	planets ";
						query << "SET ";
						query << "	planet_res_food='" << foodRest << "', ";
						query << "	planet_people='" << peopleRest << "' ";
						query << "WHERE ";
						query << "	id='" << fleet_["target_to"] << "';";
						query.store();
						query.reset();
				
						//Nachricht senden
						std::string text = "Eine Flotte vom Planet ";
						text += coordsFrom;
						text += " hat einen Antraxangriff auf den Planeten ";
						text += coordsTarget;
						text += " verübt es starben dabei ";
						text += functions::nf(functions::d2s(people));
						text += " Bewohner und ";
						text += functions::nf(functions::d2s(food));
						text += " t Nahrung wurden dabei verbrannt.";
						
						functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Antraxangriff",text);
						functions::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Antraxangriff",text);
						
						//Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
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
				functions::sendMsg((int)fleet_["user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Antraxangriff erfolglos",text);
				functions::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Antraxangriff erfolglos",text);
			}
		}
	
		if (bh->returnFleet || bh->returnV==4)
		{
			fleetReturn(1);
		}
		else
		{
			fleetDelete();
		}
	}
}

