
#include "WreckageHandler.h"

namespace wreckage
{
	void WreckageHandler::update()
	{
		/**
		* Fleet-Action: Collect wreckage/debris field
		*/ 
		
		OtherReport *report = new OtherReport(this->f->getUserId(),
											  this->f->getEntityTo(),
											  this->f->getEntityFrom(),
											  this->f->getLandtime(),
											  this->f->getId(),
											  this->f->getAction());
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			
			// Check if there is a field
			if (this->targetEntity->getWfSum()>0) {
				
				this->sum = this->targetEntity->getWfSum();
				
				// Calculate the collected resources
				if (this->f->getCapacity() <= this->targetEntity->getWfSum()) {
					this->percent = this->f->getCapacity() / this->targetEntity->getWfSum();
					this->metal = this->targetEntity->removeWfMetal(this->f->addMetal(this->targetEntity->getWfMetal() * this->percent));
					this->crystal = this->targetEntity->removeWfCrystal(this->f->addCrystal(this->targetEntity->getWfCrystal() * this->percent));
					this->plastic = this->targetEntity->removeWfPlastic(this->f->addPlastic(this->targetEntity->getWfPlastic() * this->percent));
					this->sum = this->metal + this->crystal + this->plastic;
				}
				else {
					this->metal = this->targetEntity->removeWfMetal(this->f->addMetal(this->targetEntity->getWfMetal()));
					this->crystal = this->targetEntity->removeWfCrystal(this->f->addCrystal(this->targetEntity->getWfCrystal()));
					this->plastic = this->targetEntity->removeWfPlastic(this->f->addPlastic(this->targetEntity->getWfPlastic()));
				}
				
				report->setSubtype("collectdebris");
				report->setRes(this->metal,
							   this->crystal,
							   this->plastic,
							   0,
							   0,
							   0);
				
				// Update collected resources for the userstatistic
				this->f->fleetUser->addCollectedWf(this->sum);
			}
			
			// If the field is empty
			else {
				
				// Send a message to the user
				report->setSubtype("collectdebrisfailed");
				
				this->actionLog->addText("Action failed: Entity error");
			}
		}
		
		// If there is no wreckage collecter in the fleet
		else {
			report->setSubtype("actionfailed");
			
			this->actionLog->addText("Action failed: Ship error");
		}		
		delete report;
		
		this->f->setReturn();
	}
}
