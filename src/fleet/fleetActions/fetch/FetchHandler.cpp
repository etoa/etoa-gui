
#include "FetchHandler.h"

namespace fetch
{
	void FetchHandler::update()
	{
	
		/**
		* Fleet-Action: Fetch
		*/
		Config &config = Config::instance();
		
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
				
			// Function is only allowed if the fleet user is the same as the planet user
			if (this->f->getUserId() == this->targetEntity->getUserId()) {
				
				this->f->addMetal(this->targetEntity->removeResMetal(std::min(this->f->getFetchMetal(), this->f->getCapacity()),false));
				this->f->addCrystal(this->targetEntity->removeResCrystal(std::min(this->f->getFetchCrystal(), this->f->getCapacity()),false));
				this->f->addPlastic(this->targetEntity->removeResPlastic(std::min(this->f->getFetchPlastic(), this->f->getCapacity()),false));
				this->f->addFuel(this->targetEntity->removeResFuel(std::min(this->f->getFetchFuel(), this->f->getCapacity()),false));
				this->f->addFood(this->targetEntity->removeResFood(std::min(this->f->getFetchFood(), this->f->getCapacity()),false));
				this->f->addPeople(this->targetEntity->removeResPeople(std::min(this->f->getFetchPeople(),this->f->getPeopleCapacity())));
					
				this->actionMessage->addText("[B]WAREN ABGEHOLT[/B]",2);
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[/b]hat das Ziel erreicht.[b]",1);
				this->actionMessage->addText(this->targetEntity->getCoords(),1);
				this->actionMessage->addText("[/b]Zeit: [b]");
				this->actionMessage->addText(this->f->getLandtimeString(),2);
				this->actionMessage->addText("Folgende Waren wurden abgeholt:");
				this->actionMessage->addText(this->f->getResCollectedString());
					
				this->actionMessage->addSubject("Warenabholung");
			}
			else {
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("versuchte Waren abzuholen. Leider fand die Flotte keinen deiner Planeten mehr vor und so machte sich die Crew auf den Weg nach Hause!");
				
				this->actionMessage->addSubject("Warenabholung gescheitert");
				
				this->actionLog->addText("Action failed: Planet error");
			}
		}
		else {
			this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("versuchte Waren abzuholen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
			
			this->actionMessage->addSubject("Warenabholung gescheitert");
			
			this->actionLog->addText("Action failed: Ship error");
		}
		
		this->f->setReturn();
	}
}
