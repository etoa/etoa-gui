
#include <mysql++/mysql++.h>

#include "SupportHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace support
{
	void SupportHandler::update()
	{
	
		/**
		* Fleet-Action: Support
		*/
		
		Config &config = Config::instance();
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		//Support beenden und Flotte nach Hause schicken
		if (this->f->getStatus()==3) {
			this->f->setReturn();
			
			this->actionMessage->addText("[b]SUPPORT BEENDET[/b]",2);
			this->actionMessage->addText("Eine eurer Flotten hat hat ihr Ziel verlassen und macht sich nun auf den Rückweg!",2);
			this->actionMessage->addText("[b]Zielplanet:[/b]");
			this->actionMessage->addText(this->targetEntity->getCoords(),1);
			this->actionMessage->addText("[b]Startplanet:[/b] ");
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("[b]Zeit:[/b] ");
			this->actionMessage->addText(this->f->getLandtimeString(),1);
			this->actionMessage->addText("[b]Auftrag:[/b] ");
			this->actionMessage->addText(this->f->getActionString(),1);
			
			this->actionMessage->addSubject("Supportflotte Rückflug");
							
			if (this->startEntity->getUserId() != this->f->getUserId()) {
				this->actionMessage->addUserId(this->startEntity->getUserId());
			}
		}
		//Support beginnen
		else {
			// Precheck action==possible?
			if (this->f->fleetUser->getAllianceId() == this->targetEntity->getUser()->getAllianceId()) {
				this->actionMessage->addText("[b]SUPPORTFLOTTE ANGEKOMMEN[/b]",2);
				this->actionMessage->addText("Eine Flotte hat ihr Ziel erreicht!",2);
				this->actionMessage->addText("[b]Zielplanet:[/b] ");
				this->actionMessage->addText(this->targetEntity->getCoords(),1);
				this->actionMessage->addText("[b]Startplanet:[/b] ");
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[b]Zeit:[/b] ");
				this->actionMessage->addText(this->f->getLandtimeString(),1);
				this->actionMessage->addText("[b]Auftrag:[/b] ");
				this->actionMessage->addText(this->f->getActionString(),1);
				this->actionMessage->addText("[b]Ende des Auftrages:[/b] ");
				this->actionMessage->addText(functions::formatTime(this->f->getLandtime() + this->f->getNextactiontime()),1);
				this->actionMessage->addText("[b]Flottenbesitzer:[/b] ");
				this->actionMessage->addText(this->f->fleetUser->getUserNick(),1);
				
				this->actionMessage->addSubject("Supportflotte angekommen");
				
				if (this->targetEntity->getUserId() != this->f->getUserId())
					this->actionMessage->addUserId(this->targetEntity->getUserId());
				
				this->f->setSupport();
			}
			else {
				this->actionMessage->addText("[b]FLOTTE LANDEN FEHLGESCHLAGEN[/b]",2);
				this->actionMessage->addText("Eine eurer Flotten konnte nicht auf ihrem Ziel landen!",2);
				this->actionMessage->addText("[b]Zielplanet:[/b] ");
				this->actionMessage->addText(this->targetEntity->getCoords(),1);
				this->actionMessage->addText("[b]Startplanet:[/b] ");
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[b]Zeit:[/b] ");
				this->actionMessage->addText(this->f->getLandtimeString(),1);
				this->actionMessage->addText("[b]Auftrag:[/b] ");
				this->actionMessage->addText(this->f->getActionString(),1);
				
				this->actionMessage->addSubject("Supportflug fehlgeschlagen");
				
				this->actionLog->addText("Action failed: Alliance error");
				
				this->f->setReturn();
			}
		}
	}
}
