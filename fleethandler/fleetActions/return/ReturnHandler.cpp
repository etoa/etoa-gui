
#include "ReturnHandler.h"

namespace retour
{
	void ReturnHandler::update()
	{
	
		/**
		* Fleet-Action: Returned flight
		*/
		Config &config = Config::instance();
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		if (this->targetEntity->getUserId() == this->f->getUserId()) {
			// Land fleet and delete entries in the database
			fleetLand(1);
			// Check if the user'd like to have a return message for spy and transport
		
			if (this->f->getAction()=="spy" || this->f->getAction()=="transport")
				if (!this->f->fleetUser->getPropertiesReturnMsg())
					this->actionMessage->dontSend();
			
			this->actionMessage->addText("[b]FLOTTE GELANDET[/b]",2);
			this->actionMessage->addText("Eine eurer Flotten hat ihr Ziel erreicht!",2);
			this->actionMessage->addText("[b]Ziel:[/b] ");
			this->actionMessage->addText(this->targetEntity->getCoords(),1);
			this->actionMessage->addText("[b]Start:[/b] ");
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("[b]Zeit:[/b] ");
			this->actionMessage->addText(this->f->getLandtimeString(),1);
			this->actionMessage->addText("[b]Auftrag:[/b] ");
			this->actionMessage->addText(this->f->getActionString(),1);
			
			this->actionMessage->addSubject("Flotte angekommen");
		}
		
		// If the planet user is not the same as the fleet user, send fleet to the main and send a message with the info
		else {
			fleetSendMain();
			
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
			
			this->actionLog->addText("Action Failed: Planet error");
		}
	}
}
