#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "TransportHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace transport
{
	void TransportHandler::update()
	{
	
		/**
		* Fleet-Action: Transport
		*/
		
		Config &config = Config::instance();	
		//Waren ausladen
		fleetLand(2); //ToDo init time

		//Sucht User-ID
		int userToId = functions::getUserIdByPlanet((int)fleet_["fleet_entity_to"]);

		std::string msg = "[B]TRANSPORT GELANDET[/B]\n\nEine Flotte vom Planeten \n[b]";
		msg += functions::formatCoords((int)fleet_["fleet_entity_from"],0);
		msg += "[/b]\nhat ihr Ziel erreicht!\n\n[b]Planet:[/b] ";
		msg += functions::formatCoords((int)fleet_["fleet_entity_to"],0);
		msg += "\n[b]Zeit:[/b] ";
		msg += functions::formatTime((int)fleet_["fleet_landtime"]);
		msg += "\n";
		msg += msgRes;
	
		// Nachrichten senden
		functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Transport angekommen",msg);
	
		//Nachricht an Empfänger senden, falls Empfänger != Sender
		if ((int)fleet_["fleet_user_id"]!=userToId)
		{
			functions::sendMsg(userToId,config.idget("SHIP_MISC_MSG_CAT_ID"),"Transport angekommen",msg);
		}
		
		double capacity = (double)fleet_["fleet_res_metal"] + (double)fleet_["fleet_res_crystal"] + (double)fleet_["fleet_res_plastic"] + (double)fleet_["fleet_res_fuel"] + (double)fleet_["fleet_res_food"];
		// Flotte zurückschicken & Waren aus dem Frachtraum löschen
		fleetReturn("tr",0,0,0,0,0,0,capacity);

		// Handel loggen falls der transport an einen anderen user ging
		if((int)fleet_["fleet_user_id"] != userToId)
		{
			std::string log = "Der Spieler [URL=?page=user&sub=edit&user_id=";
			log += std::string(fleet_["fleet_user_id"]);
			log += "][B]";
			log += functions::getUserNick((int)fleet_["fleet_user_id"]);
			log += "[/B][/URL] sendet dem Spieler [URL=?page=user&sub=edit&user_id=";
			log += functions::d2s(userToId);
			log += "][B]";
			log += functions::getUserNick(userToId);
			log += "[/B][/URL] folgende Rohstoffe\n\n";
			log += msgRes;
			functions::addLog(11,log,(int)time);
		}
	}
}
