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
		std::time_t time = std::time(0);	

		// Unload the resources
		fleetLand(2);

		// Send a message to the fleet user
		std::string msg = "[B]TRANSPORT GELANDET[/B]\n\nEine Flotte vom Planeten \n[b]";
		msg += this->f->getEntityFromString(0);
		msg += "[/b]\nhat ihr Ziel erreicht!\n[b]Planet:[/b] ";
		msg += this->f->getEntityToString(0);
		msg += "\n[b]Zeit:[/b] ";
		msg += this->f->getLandtimeString();
		msg += msgRes;
	
		functions::sendMsg(this->f->getUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Transport angekommen",msg);
	
		// If the planet user is not the same as the fleet user, send him a message too
		if (this->f->getUserId() != this->f->getEntityToUserId()) {
			functions::sendMsg(this->f->getEntityToUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Transport angekommen",msg);
			
			// Add a log
			std::string log = "Der Spieler [URL=?page=user&sub=edit&user_id=";
			log += functions::d2s(this->f->getUserId());
			log += "][B]";
			log += functions::getUserNick(this->f->getUserId());
			log += "[/B][/URL] sendet dem Spieler [URL=?page=user&sub=edit&user_id=";
			log += functions::d2s(this->f->getEntityToUserId());
			log += "][B]";
			log += functions::getUserNick(this->f->getEntityToUserId());
			log += "[/B][/URL] folgende Rohstoffe\n\n";
			log += msgRes;
			functions::addLog(11,log,(int)time);
		}
		
		// Send fleet back home and delete the resources tonnage
		fleetReturn(1,0,0,0,0,0,0);
	}
}
