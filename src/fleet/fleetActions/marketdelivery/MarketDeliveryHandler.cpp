
#include "MarketDeliveryHandler.h"

namespace marketdelivery
{
	void MarketDeliveryHandler::update()
	{
	
		/**
		* Fleet-Action: Market-delivery
		*/
		Config &config = Config::instance();
		
		//Send no message to the fleet user
		this->actionMessage->dontSend();
		
		//create a message for the entity user
		Message *marketMesage = new Message();
		
		// Deliver ships and resources
		if (this->f->getCapacity()>0) {
			// Land fleet and save the resources and the ships on the planet
			fleetLand(1);
			
			marketMesage->addSubject("Flotte vom Handelsministerium");
		}
		
		// If there were only resources delivered
		else {
			// Save them on the planet
			fleetLand(2);
			
			marketMesage->addSubject("Transport vom Handelsministerium");
		}
		
		// Send a message to the user
		marketMesage->addText("Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:",1);
		marketMesage->addText("[b]Planet:[/b] ");
		marketMesage->addText(this->targetEntity->getCoords(),1);
		marketMesage->addText("[b]Zeit:[/b] ");
		marketMesage->addText(this->f->getLandtimeString(),1);
		marketMesage->addText("[b]Bericht:[/b] Die gekauften Waren sind angekommen.",1);
		marketMesage->addUserId(this->targetEntity->getUserId());
		
		marketMesage->addSignature("Unser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium");
		
		marketMesage->addUserId(this->targetEntity->getUserId());
		marketMesage->addEntityId(this->targetEntity->getId());
		marketMesage->addFleetId(this->f->getId());
		marketMesage->addType((int)config.idget("SHIP_MONITOR_MSG_CAT_ID"));
		
		delete marketMesage;
		
		//Delete fleet
		this->f->setPercentSurvive(0);
	}
}
