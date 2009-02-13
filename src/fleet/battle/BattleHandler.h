
#ifndef __BATTLEHANDLER__
#define __BATTLEHANDLER__

#include <ctime>
#include <cstdio>
#include <mysql++/mysql++.h>

#include "../../MysqlHandler.h"
#include "../../config/ConfigHandler.h"
#include "../../util/Functions.h"

#include "../../objects/Fleet.h"
#include "../../entity/Entity.h"
#include "../../objects/Message.h"
#include "../../objects/Log.h"

/**
* Handles battles....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/

class BattleHandler
{
	public:
		BattleHandler(Message* message) {
			this->message = new Message(message);
		}
		void battle(Fleet* fleet, Entity* entity, Log* log);
		
		~BattleHandler() {	}
		
		Message* message;
		
		bool alliancesHaveWar;
		
		short runde;
		
		int attPoints,defPoints;
		
		short returnV;
		std::string bstat, bstat2;
		bool returnFleet;
		
};
#endif
