
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
			
			ExploreReport *report = new ExploreReport(this->f->getUserId(),
													  this->f->getEntityTo(),
													  this->f->getEntityFrom(),
													  this->f->getLandtime());
			report->setType("explore");
			delete report;
		}	
		else {
			OtherReport *report = new OtherReport(this->f->getUserId(),
												this->f->getEntityTo(),
												this->f->getEntityFrom(),
												this->f->getLandtime(),
												this->f->getId(),
												this->f->getAction());
			report->setSubtype("actionfailed");

			delete report;
			
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
