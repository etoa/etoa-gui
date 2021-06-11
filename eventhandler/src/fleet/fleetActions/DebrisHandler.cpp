
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

		OtherReport *report = new OtherReport(this->f->getUserId(),
										this->f->getEntityTo(),
										this->f->getEntityFrom(),
										this->f->getLandtime(),
										this->f->getId(),
										this->f->getAction());

		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			// Create a debris field with the fleet (every single ship 40% of the costs)
			this->f->setPercentSurvive(0);
			this->targetEntity->addWfMetal(this->f->getWfMetal());
			this->targetEntity->addWfCrystal(this->f->getWfCrystal());
			this->targetEntity->addWfPlastic(this->f->getWfPlastic());

			report->setSubtype("createdebris");
		}

		// If no ship with the action was in the fleet
		else {

			report->setSubtype("actionfailed");

			this->actionLog->addText("Action failed: Ship error");
		}
		delete report;
		this->f->setReturn();
	}
}
