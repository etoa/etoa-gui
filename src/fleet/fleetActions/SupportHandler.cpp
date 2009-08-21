
#include "SupportHandler.h"

namespace support
{
	void SupportHandler::update()
	{
	
		/**
		* Fleet-Action: Support
		*/
		
		OtherReport *report = new OtherReport(this->f->getUserId(),
											  this->f->getEntityTo(),
											  this->f->getEntityFrom(),
											  this->f->getLandtime(),
											  this->f->getId(),
											  this->f->getAction());
		
		//Support beenden und Flotte nach Hause schicken
		if (this->f->getStatus()==3) {
			this->f->setReturn();
			
			report->setSubtype("supportreturn");
							
			if (this->startEntity->getUserId() != this->f->getUserId())
				report->addUser(this->startEntity->getUserId());
		}
		//Support beginnen
		else {
			// Precheck action==possible?
			if (this->f->fleetUser->getAllianceId() == this->targetEntity->getUser()->getAllianceId()) {
				report->setOpponent1Id(this->f->getUserId());
				
				report->setSubtype("support");
				report->setContent(etoa::d2s(this->f->getLandtime() + this->f->getNextactiontime()));
				
				if (this->targetEntity->getUserId() != this->f->getUserId())
					report->addUser(this->targetEntity->getUserId());
				
				this->f->setSupport();
			}
			else {
				report->setSubtype("supportfailed");
				
				this->actionLog->addText("Action failed: Alliance error");
				
				this->f->setReturn();
			}
		}
		delete report;
	}
}
