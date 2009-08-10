
#include "TransportHandler.h"

namespace transport
{
	void TransportHandler::update()
	{
	
		/**
		* Fleet-Action: Transport
		*/
		
		OtherReport *report = new OtherReport(this->f->getUserId(),
											  this->f->getEntityTo(),
											  this->f->getEntityFrom(),
											  this->f->getLandtime(),
											  this->f->getId(),
											  this->f->getAction(true));
		report->setSubtype("transport");
		report->setRes(floor(this->f->getResMetal()),
					   floor(this->f->getResCrystal()),
					   floor(this->f->getResPlastic()),
					   floor(this->f->getResFuel()),floor(this->f->getResFood()),
					   floor(this->f->getResPeople()));
		
		report->setAction(this->f->getAction(true));
		report->setStatus(this->f->getStatus());
		
		// If the planet user is not the same as the fleet user, send him a message too
		if (this->f->getUserId() != this->targetEntity->getUserId()) {
			report->addUser(this->targetEntity->getUserId());
			
			this->actionLog->addText("Action succeed: Transport 2 User");
		}
		
		delete report;
		
		// Unload the resources
		this->fleetLand(2);
		
		// Send fleet back home and delete the resources tonnage
		this->f->setReturn();
	}
}
