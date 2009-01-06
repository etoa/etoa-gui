
#ifndef __BATTLEHANDLER__
#define __BATTLEHANDLER__

#include <ctime>

#include "../config/ConfigHandler.h"
#include "../functions/Functions.h"

#include "../objects/Fleet.h"
#include "../entity/Entity.h"
#include "../objects/Message.h"
#include "../objects/Log.h"

/**
* Handles battles....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/

class BattleHandler
{
	public:
		BattleHandler() { }
		void battle(Fleet* fleet, Entity* entity, Message* message, Log* log);

		bool alliancesHaveWar;
		
		short runde;
		
		short returnV;
		std::string bstat, bstat2;
		bool returnFleet;
		
};
#endif
