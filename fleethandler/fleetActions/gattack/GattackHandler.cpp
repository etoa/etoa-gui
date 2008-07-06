#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "GattackHandler.h"
#include "../../MysqlHandler.H"
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
	/*	Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);

		
		BattleHandler *bh = new BattleHandler(con_,fleet_);
		bh->battle();

		// Send messages
		int userToId = functions::getUserIdByPlanet((int)fleet_["fleet_entity_to"]);
		std::string subject1 = "Kampfbericht (";
		subject1 += bstat;
		subject1 += ")";
		std::string subject2 = "Kampfbericht (";
		subject2 += bstat2;
		subject2 += ")";
		functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),subject1,bh->msg);
		functions::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),subject2,bh->msg);

		// Add log
		functions::addLog(1,bh->msg,(int)fleet_["fleet_landtime"]);

		// Aktion durchführen
		if (bh->returnV==1)
		{
			bh->returnFleet = true;
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
			query << "	AND techlist_tech_id='" << config.idget("Gifttechnologie") << "';";
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
			double goOrNot = rand() % 101;
			if (goOrNor<=(config.nget("gasattack_action",0) + tLevel * 5 + bh->specialShipBonusAntrax * 100))
			{
				std::string coordsTarget = functions::formatCoords(fleet_["fleet_entity_to"]);
				std::string coordsFrom = functions::formatCoords(fleet_["fleet_entity_from"]);
				
				//Rechnet Prozent der Bevölkerung, die ausgelöscht werden (Max. 95%)
				double temp = std::min((25+tLevel*3),config.nget("gasattack_action",0));
				double percent = rand() % temp;
				
				//Lädt Anzahl Bewohner
				query << "SELECT ";
				query << "	planet_people ";
				query << "FROM ";
				query << "	planets ";
				query << "WHERE ";
				query << "	id='" << fleet_["fleet_entity_to"] << "';";
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
						query << "	id='" << fleet_["fleet_entity_to"] << "';";
						query.store();
						query.reset();
					
						//Nachricht senden
						std::string text = "Eine Flotte vom Planet ";
						text += coordsFrom;
						text += " hat einen Giftgasangriff auf den Planeten ";
						text += coordsTarget;
						text += " verübt es starben dabei ";
						text += functions::nf(functions::d2s(rest));
						text += " Bewohner";
						
						functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Giftgasangriff",text);
						functions::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Giftgasangriff",text);

						//Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
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
				
				functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_WAR_MSG_CAT_ID"),"Giftgasangriff erfolglos",text);
				functios::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),"Giftgasangriff erfolglos",text);
			}
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
