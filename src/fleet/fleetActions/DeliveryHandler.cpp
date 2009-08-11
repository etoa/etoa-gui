
#include "DeliveryHandler.h"

namespace delivery
{
	void DeliveryHandler::update()
	{
	
		/**
		* Fleet-Action: Market-delivery
		*/
		
		OtherReport *report = new OtherReport(this->f->getUserId(),
											  this->f->getEntityTo(),
											  this->f->getEntityFrom(),
											  this->f->getLandtime(),
											  this->f->getId(),
											  this->f->getAction());
		
		report->setStatus(this->f->getStatus());

		// Precheck, watch if the buyer is the same as the planet user
		if (this->targetEntity->getUserId() == this->f->getUserId()) {
			report->setShips(this->f->getShipString());
			report->setSubtype("delivery");
			// Deliver ships
			fleetLand(1);

		}
		
		// If the planet user is not the same as the buyer, send fleet to the main and send a message with the info
		else {
			report->setSubtype("deliveryfailed");
			
			this->f->setMain();
			
			this->actionLog->addText("Action Failed: Planet error");
		}
	}
}
