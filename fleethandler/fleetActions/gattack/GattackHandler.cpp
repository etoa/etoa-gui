#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "GattackHandler.h"
#include "../../MysqlHandler.H"
#include "../../functions/Functions.h"

namespace gattack
{
	void GattackHandler::update()
	{
	
		/**
		* Fleet-Action: Gas-Attack
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
			if (goOrNor<=(40+tLevel*5+$special_ship_bonus_antrax*100)) //ToDo
			{
				std::string coordsTarget = functions::formatCoords(fleet_["fleet_target_to"]);
				std::string coordsFrom = functions::formatCoords(fleet_["fleet_target_from"]);
				
				//Rechnet Prozent der Bevölkerung, die ausgelöscht werden (Max. 95%)
				double percent = mt_rand(1,min((25+tLevel*3),95)); //ToDo
				
				//Lädt Anzahl Bewohner
				query << "SELECT ";
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
				
						//Rechnet Bewohner (Neue Anzahl und Verlust)
						double people = round((double)pRow["planet_people"] * percent / 100);
						double rest = round((double)pRow["planet_people"] - people);
				
						//Bewohner werden abgezogen
						query << "UPDATE ";
						query << "	planets ";
						query << "SET ";
						query << "	planet_people='" << people << "' ";
						query << "WHERE ";
						query << "	id='" << fleet_["fleet_target_to"] << "';";
						query.store();
						query.reset();
					
						//Nachricht senden
						std::string text = "Eine Flotte vom Planet ";
						text += coordsFrom;
						text += " hat einen Giftgasangriff auf den Planeten ";
						text += coordsTarget;
						text += " verübt es starben dabei ";
						text += functions::nf(std::string(rest));
						text += " Bewohner";
						
						functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Giftgasangriff",text);
						functions::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Giftgasangriff",text);

						Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
					}
				}
			} 
			else 
			{
				//Nachricht senden (Giftgasangriff fehlgeschlagen)
				std::string text = "Eine Flotte vom Planet ";
				text += coordsFrom;
				text += " hat erfolglos einen Giftgasangriff auf den Planeten ";
				text += coordsTarget;
				text += " verübt.";
				
				functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_WAR_MSG_CAT_ID,"Giftgasangriff erfolglos",text);
				functios::sendMsg(userToId,SHIP_WAR_MSG_CAT_ID,"Giftgasangriff erfolglos",text);
			}
		}

		if (returnFleet || returnV==4)
		{
			fleetReturn("xr");
		}
		else
		{
			fleetDelete();
		}
	}
}

