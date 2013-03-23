
#include "SupportHandler.h"

namespace support
{
	void SupportHandler::update()
	{
	
		/**
		* Fleet-Action: Support
		*/
		
		Config &config = Config::instance();
		
		OtherReport *report = new OtherReport(this->f->getUserId(),
											  this->f->getEntityTo(),
											  this->f->getEntityFrom(),
											  this->f->getLandtime(),
											  this->f->getId(),
											  this->f->getAction());
		// status == 3 means fleet is supporting planet
		//Support beenden und Flotte nach Hause schicken
		if (this->f->getStatus()==3) {
			this->f->setReturn();
			
			report->setSubtype("supportreturn");
							
			if (this->startEntity->getUserId() != this->f->getUserId())
				report->addUser(this->startEntity->getUserId());
		}
		// check whether planet has no more support slots
		else if
		(
		    // if config =0, support slots aren't limited
		    config.nget("alliance_fleets_max_players",0)
		    // a user can always support his own planets
		    && !(this->f->fleetUser->getUserId() == this->targetEntity->getUserId())
		    // call the check function with the config value, target id and target user
		    && !this->f->checkSupportSlots
		    (
			(unsigned int)config.nget("alliance_fleets_max_players",1),
			this->targetEntity->getId(),
			this->targetEntity->getUserId()
		    )
		)
		{
		    // no support slots available
		    report->setSubtype("supportoverflow");
		    // send fleet back home
		    // NOTE: this leads to unexpected fleet return times
		    this->f->setReturn();
		}
		//Support beginnen
		else {
			// Precheck action==possible?
			// Alliance == 0 => no alliance; support only for same-alliance users
			// if target belongs to same user, no alliance is needed
			if (this->f->fleetUser->getUserId() == this->targetEntity->getUserId() || (this->f->fleetUser->getAllianceId() > 0 && this->f->fleetUser->getAllianceId() == this->targetEntity->getUser()->getAllianceId())) {
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
