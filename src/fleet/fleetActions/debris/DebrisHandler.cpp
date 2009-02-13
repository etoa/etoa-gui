
#include "DebrisHandler.h"

namespace debris
{
	void DebrisHandler::update()
	{
	
		/**
		* Fleet-Action: Create debris field
		*
		* Whole fleet will be destroyed!
		*/ 
		
		Config &config = Config::instance();
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			// Create a debris field with the fleet (every single ship 40% of the costs)
			this->f->setPercentSurvive(0);
			this->targetEntity->addWfMetal(this->f->getWfMetal());
			this->targetEntity->addWfCrystal(this->f->getWfCrystal());
			this->targetEntity->addWfPlastic(this->f->getWfPlastic());

			// Send a message to the fleet user
			this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("[/b]hat auf dem Planeten [b]");
			this->actionMessage->addText(this->targetEntity->getCoords(),1);
			this->actionMessage->addText("[/b]ein Trümmerfeld erstellt.");
			
			this->actionMessage->addSubject("Tr&uuml;mmerfeld erstellt");
		}
		
		// If no ship with the action was in the fleet
		else {
			this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("versuchte ein Trümmerfeld zu erstellen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
			
			this->actionMessage->addSubject("Trümmerfeld erstellen gescheitert");
			
			this->actionLog->addText("Action failed: Ship error");
		}
		
		this->f->setReturn();
	}
}
