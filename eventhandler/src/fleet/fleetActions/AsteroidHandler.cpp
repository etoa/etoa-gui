
#include "AsteroidHandler.h"

namespace asteroid
{
	void AsteroidHandler::update()
	{
	
		/**
		* Fleet-Action: Collect asteroids
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
			if (this->targetEntity->getCode()=='a' && this->targetEntity->getResSum()>0) {
				report->setSubtype("collectmetal");
				
				this->one = rand() % 101;
				this->two = (int)(config.nget("asteroid_action",0));
				
				// Ship were destroyed?
				if (this->one  < this->two)	{
					int percent = 100 - rand() % (int)(config.nget("asteroid_action",1));
					this->f->setPercentSurvive(percent/100.0);
				}
				
				report->setShips(this->f->getDestroyedShipString());
				
				if (this->f->actionIsAllowed()) {
					this->metal = config.nget("asteroid_action",2) + (rand() % (int)(this->f->getActionCapacity()/3 - config.nget("asteroid_action",2) + 1));
					this->metal = this->f->addMetal(this->targetEntity->removeResMetal(std::min(this->metal,this->targetEntity->getResMetal())));
					
					this->crystal = config.nget("asteroid_action",2) + (rand() % (int)(this->f->getActionCapacity()/3 - config.nget("asteroid_action",2) + 1));
					this->crystal = this->f->addCrystal(this->targetEntity->removeResCrystal(std::min(this->crystal,this->targetEntity->getResCrystal())));
					
					this->plastic = config.nget("asteroid_action",2) + (rand() % (int)(this->f->getActionCapacity()/3 - config.nget("asteroid_action",2) + 1));
					this->plastic = this->f->addPlastic(this->targetEntity->removeResPlastic(std::min(this->plastic,this->targetEntity->getResPlastic())));
					
					this->sum = this->metal + this->crystal + this->plastic;
					
					report->setRes(floor(metal),
								   floor(crystal),
								   floor(plastic));
					
					// Save the collected resources
					this->f->fleetUser->addCollectedAsteroid(this->sum);
				}
				
				// If there arent any asteroid collecter anymore
				else {
					report->setSubtype("actionshot");
					this->actionLog->addText("Action failed: Shot error");
				}
			}
			// If the asteroid field isnt there anymore
			else {
				report->setSubtype("collectmetalfailed");
				
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
