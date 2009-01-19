
#include "DeliveryHandler.h"

namespace delivery
{
	void DeliveryHandler::update()
	{
	
		/**
		* Fleet-Action: Market-delivery
		*/
		Config &config = Config::instance();
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));

		// Precheck, watch if the buyer is the same as the planet user
		if (this->targetEntity->getUserId() == this->f->getUserId()) {
			// Deliver ships
			fleetLand(1);

			this->actionMessage->addText("Eine Flotte von der Allianzbasis hat folgendes Ziel erreicht:",1);
			this->actionMessage->addText("[b]Planet:[/b] ");
			this->actionMessage->addText(this->targetEntity->getCoords(),1);
			this->actionMessage->addText("[b]Zeit:[/b] ");
			this->actionMessage->addText(this->f->getLandtimeString(),1);
			this->actionMessage->addText("[b]Bericht:[/b] Die erstellten Schiffe sind gelandet.",1);
				
			this->actionMessage->addSubject("Flotte von der Allianzbasis");
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
