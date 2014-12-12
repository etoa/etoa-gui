
#include "MarketDeliveryHandler.h"

namespace marketdelivery
{
	void MarketDeliveryHandler::update()
	{
	
		/**
		* Fleet-Action: Market-delivery
		*/
		
		//create a message for the entity user
		OtherReport *report = new OtherReport(this->targetEntity->getUserId(),
											  this->f->getEntityTo(),
											  this->f->getEntityFrom(),
											  this->f->getLandtime(),
											  this->f->getId(),
											  this->f->getAction());
		report->setSubtype("market");
		report->setStatus(this->f->getStatus());
		report->setRes(floor(this->f->getResMetal()),
					   floor(this->f->getResCrystal()),
					   floor(this->f->getResPlastic()),
					   floor(this->f->getResFuel()),floor(this->f->getResFood()),
					   floor(this->f->getResPeople()));
		report->setShips(this->f->getShipString());
		
		delete report;
		
		fleetLand(1);
		
		//Delete fleet
		this->f->setPercentSurvive(0);
	}
}
