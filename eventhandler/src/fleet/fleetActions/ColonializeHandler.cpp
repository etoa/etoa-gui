
#include "ColonializeHandler.h"

namespace colonialize
{
	void ColonializeHandler::update()
	{
	
		/**
		* Fleet-Action: Colonialize
		*/
		Config &config = Config::instance();
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			OtherReport *report = new OtherReport(this->f->getUserId(),
												  this->f->getEntityTo(),
												  this->f->getEntityFrom(),
												  this->f->getLandtime(),
												  this->f->getId(),
												  this->f->getAction());
			if (this->targetEntity->getUserId()) {
			
				if(this->targetEntity->getUserId() == this->f->getUserId()) {
					report->setSubtype("return");
					report->setRes(floor(this->f->getResMetal()),
								   floor(this->f->getResCrystal()),
								   floor(this->f->getResPlastic()),
								   floor(this->f->getResFuel()),floor(this->f->getResFood()),
								   floor(this->f->getResPeople()));
					report->setShips(this->f->getShipString());
					report->setStatus(this->f->getStatus());

					fleetLand(1);
				}
				// If the planet belongs to en other user, return the fleet back home
				else {
					report->setSubtype("colonizefailed");
					report->setContent("1");
				}
			}
			// if the planet has not yet a user
			else {
				// if the user has already enough planets 
				if (this->f->fleetUser->getPlanetsCount() >= (int)config.nget("user_max_planets",0)) {
					report->setSubtype("colonizefailed");
					report->setContent("2");
				}
				// if up to now everything is fine, let's colonialize the planet
				else {
					// reset the planet
					this->targetEntity->resetEntity(this->f->getUserId());
					report->setSubtype("colonize");
					
					report->setRes(floor(this->f->getResMetal()),
								   floor(this->f->getResCrystal()),
								   floor(this->f->getResPlastic()),
								   floor(this->f->getResFuel()),floor(this->f->getResFood()),
								   floor(this->f->getResPeople()));
					
					// Land the fleet and delete one ship (action colonialize)
					this->f->deleteActionShip(1);
					report->setShips(this->f->getShipString());
					fleetLand(1);
				}
			}
			delete report;
		}
		// If no ship with the action was in the fleet 
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
