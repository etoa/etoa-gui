
#include "MarketDeliveryHandler.h"

namespace marketdelivery
{
	void MarketDeliveryHandler::update()
	{
	
		/**
		* Fleet-Action: Market-delivery
		*/
		Config &config = Config::instance();
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		// Precheck, watch if the buyer is the same as the planet user
		if (this->targetEntity->getUserId() == this->f->getUserId()) {
			// Deliver ships and resources
			if (this->f->getCapacity()>0) {
				// Land fleet and save the resources and the ships on the planet
				fleetLand(1);

				// Send a message to the user
				this->actionMessage->addText("Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:",1);
				this->actionMessage->addText("[b]Planet:[/b] ");
				this->actionMessage->addText(this->targetEntity->getCoords(),1);
				this->actionMessage->addText("[b]Zeit:[/b] ");
				this->actionMessage->addText(this->f->getLandtimeString(),1);
				this->actionMessage->addText("[b]Bericht:[/b] Die gekauften Waren sind angekommen.",1);
				
				this->actionMessage->addSignature("Unser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium");
				
				this->actionMessage->addSubject("Flotte vom Handelsministerium");
			}
	
			// If there were only resources delivered
			else {
				// Save them on the planet
				fleetLand(2);

				// Send a message to the planet user
				this->actionMessage->addText("Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:",1);
				this->actionMessage->addText("[b]Planet:[/b] ");
				this->actionMessage->addText(this->targetEntity->getCoords(),1);
				this->actionMessage->addText("[b]Zeit:[/b] ");
				this->actionMessage->addText(this->f->getLandtimeString(),1);
				this->actionMessage->addText("[b]Bericht:[/b] Die gekauften Waren sind angekommen.",1);
				
				this->actionMessage->addSignature("Unser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium");
				
				this->actionMessage->addSubject("Transport vom Handelsministerium");
				
				this->f->setPercentSurvive(0);
			}

			// Delete the fleet data
		}
		
		// If the planet user is not the same as the buyer, send fleet to the main and send a message with the info
		else {
			this->actionMessage->addText("[b]FLOTTE LANDEN GESCHEITERT[/b]",2);
			this->actionMessage->addText("Eine eurer Flotten hat versucht auf ihrem Ziel zu laden Der Versuch scheiterte jedoch und die Flotte macht sich auf den Weg zu eurem Hauptplaneten!",2);
			this->actionMessage->addText("[b]Ziel:[/b] ");
			this->actionMessage->addText(this->targetEntity->getCoords(),1);
			this->actionMessage->addText("[b]Start:[/b] ");
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("[b]Zeit:[/b] ");
			this->actionMessage->addText(this->f->getLandtimeString(),1);
			this->actionMessage->addText("[b]Auftrag:[/b] ");
			this->actionMessage->addText(this->f->getActionString(),1);
			
			this->actionMessage->addSubject("Flotte umgelenkt");
			
			this->f->setMain();
			
			this->actionLog->addText("Action Failed: Planet error");
		}
	}
}
