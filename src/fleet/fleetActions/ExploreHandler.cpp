
#include "ExploreHandler.h"

namespace explore
{
	void ExploreHandler::update()
	{
	
		/**
		* Fleet-Action: Explore the univserse
		*/
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			
			this->f->fleetUser->setDiscovered(this->targetEntity->getAbsX(),this->targetEntity->getAbsY());
			
			this->actionMessage->dontSend();
			
			ExploreReport *report = new ExploreReport(this->f->getUserId(),
													  this->f->getEntityTo(),
													  this->f->getEntityFrom(),
													  this->f->getLandtime());
			delete report;
		}	
		else {
			Config &config = Config::instance();
			
			this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
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
