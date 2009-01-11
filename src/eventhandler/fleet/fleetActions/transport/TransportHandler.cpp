
#include "TransportHandler.h"

namespace transport
{
	void TransportHandler::update()
	{
	
		/**
		* Fleet-Action: Transport
		*/
		
		Config &config = Config::instance();
		
		// Unload the resources
		this->fleetLand(2);
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		this->actionMessage->addText("[B]TRANSPORT GELANDET[/B]",2);
		this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
		this->actionMessage->addText(this->startEntity->getCoords(),1);
		this->actionMessage->addText("[/b]hat ihr Ziel erreicht!",1);
		this->actionMessage->addText("[b]Planet:[/b] ");
		this->actionMessage->addText(this->targetEntity->getCoords(),1);
		this->actionMessage->addText("[b]Zeit:[/b] ");
		this->actionMessage->addText(this->f->getLandtimeString(),1);
		
		this->actionMessage->addSubject("Transport angekommen");
		
		// If the planet user is not the same as the fleet user, send him a message too
		if (this->f->getUserId() != this->targetEntity->getUserId()) {
			this->actionMessage->addUserId(this->targetEntity->getUserId());
			
			this->actionLog->addText("Action succeed: Transport 2 User");
		}
		// Send fleet back home and delete the resources tonnage
		this->f->setReturn();
	}
}
