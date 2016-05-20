
#include "PositionHandler.h"

namespace position
{
	void PositionHandler::update()
	{
	
		/**
		* Fleet-Action: Position
		*/
		
		if (this->targetEntity->getUserId() == this->f->getUserId()) {
			// Check if the user'd like to have a return message for spy and transport
		
			if (!(this->f->getAction()=="spy" || this->f->getAction()=="transport") &&!this->f->fleetUser->getPropertiesReturnMsg()) {
				OtherReport *report = new OtherReport(this->f->getUserId(),
													  this->f->getEntityTo(),
													  this->f->getEntityFrom(),
													  this->f->getLandtime(),
													  this->f->getId(),
													  this->f->getAction());
				report->setSubtype("return");
				report->setRes(floor(this->f->getResMetal()),
							   floor(this->f->getResCrystal()),
							   floor(this->f->getResPlastic()),
							   floor(this->f->getResFuel()),floor(this->f->getResFood()),
							   floor(this->f->getResPeople()));
				report->setShips(this->f->getShipString());
				report->setStatus(this->f->getStatus());
				
				delete report;
			}
			// Land fleet and delete entries in the database
			fleetLand(1);
		}
		
		// If the planet user is not the same as the fleet user, send fleet to the main and send a message with the info
		else {
			OtherReport *report = new OtherReport(this->f->getUserId(),
												  this->f->getEntityTo(),
												  this->f->getEntityFrom(),
												  this->f->getLandtime(),
												  this->f->getId(),
												  this->f->getAction());
			report->setSubtype("actionmain");
			report->setShips(this->f->getShipString());
			report->setStatus(this->f->getStatus());
			
			delete report;
			
			this->f->setMain();
			
			this->actionLog->addText("Action Failed: Planet error");
		}
	}
}
