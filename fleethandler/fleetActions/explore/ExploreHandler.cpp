
#include "ExploreHandler.h"

namespace explore
{
	void ExploreHandler::update()
	{
	
		/**
		* Fleet-Action: Explore the univserse
		*/
		
		Config &config = Config::instance();
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			
			this->f->fleetUser->setDiscovered(this->targetEntity->getAbsX(),this->targetEntity->getAbsY());
			
			this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("[/b]hat das Ziel [b]",1);
			this->actionMessage->addText(this->targetEntity->getCoords(),1);
			this->actionMessage->addText("[/b]um [b]");
			this->actionMessage->addText(this->f->getLandtimeString(),1);
			this->actionMessage->addText("[/b]erkundet.");
			
			this->actionMessage->addSubject("Erkundung");
		}	
		else {
			this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("versuchte das Ziel zu erkunden. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
			
			this->actionMessage->addSubject("Erkundung gescheitert");
			
			this->actionLog->addText("Action failed: Ship error");
		}
		
		this->f->setReturn();
	}
}
						
	
	/*std::string ExploreHandler::event()
	{
	
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		
		this->days = (time - lastvisited)/3600;
		
		this->one = rand() % 101;
		
		if (code=='p' || code=='s')
		{
			this-> = std::min(days*3.30),5);
		}
		else
		{
			this->two = std::min(days*5,50),80);
		}
		
		if (this->one < this->two)
		{
		 
		}
		//Sonst keine Aktion
		else
		{
		  
		}
	}
}*/
