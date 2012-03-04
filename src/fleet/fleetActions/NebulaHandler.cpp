
#include "NebulaHandler.h"

namespace nebula
{
	void NebulaHandler::update()
	{
	
		/**
		* Fleet action: Collect nebula gas
		*/

		Config &config = Config::instance();
		
		OtherReport *report = new OtherReport(this->f->getUserId(),
											  this->f->getEntityTo(),
											  this->f->getEntityFrom(),
											  this->f->getLandtime(),
											  this->f->getId(),
											  this->f->getAction());
		report->setStatus(this->f->getStatus());
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			// Check if there is a field
			if (this->targetEntity->getCode()=='n' && this->targetEntity->getResSum()>0) {
				report->setSubtype("collectcrystal");
				
				this->one = rand() % 101;
				this->two = (int)config.nget("nebula_action",0);
				
				// Ship were destroyed?
				if (this->one  < this->two)	{
					int percent = 100 - rand() % (int)config.nget("nebula_action",1);
					
					this->f->setPercentSurvive(percent/100.0);
				}
				
				report->setShips(this->f->getDestroyedShipString());
				
				if (this->f->actionIsAllowed()) {
					this->sum = 0;
					
					this->nebula = config.nget("nebula_action",2) + (rand() % (int)(this->f->getActionCapacity() - config.nget("nebula_action",2) + 1));
					this->sum +=this->f->addCrystal(this->targetEntity->removeResCrystal(std::min(this->nebula,this->targetEntity->getResCrystal())));
					
					report->setRes(0,
								   floor(this->sum));
					
					// Save the collected resources
					this->f->fleetUser->addCollectedNebula(this->sum);
					
				}
				// if there are no nebula collecter in the fleet anymore
				else {
					report->setSubtype("actionshot");					
					this->actionLog->addText("Action failed: Shot error");	
				}
			}
			// If the asteroid field isnt there anymore
			else {
				report->setSubtype("collectcrystalfailed");
				
				this->actionLog->addText("Action failed: entity error");
			}
		}
		
		// If there isnt any asteroid colecter in the fleet 
		else {
			report->setSubtype("actionfailed");
			
			this->actionLog->addText("Action failed: Ship error");
		}
		
		delete report;
		this->f->setReturn();
	}
}
