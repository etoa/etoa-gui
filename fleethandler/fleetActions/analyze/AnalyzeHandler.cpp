
#include "AnalyzeHandler.h"

namespace analyze
{
	void AnalyzeHandler::update()
	{
	
		/**
		* Fleet action: Analyze
		*/
		
		Config &config = Config::instance();
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			// If entity is a neulafield
			if (this->targetEntity->getCode()=='n') {
				
				// Sending a message to the User with the data				
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[/b]hat das[b] ");
				this->actionMessage->addText(this->targetEntity->getCoords(),1);
				this->actionMessage->addText("[/b]um [b]");
				this->actionMessage->addText(this->f->getLandtimeString(),1);
				this->actionMessage->addText("[/b] analysiert.",2);
				this->actionMessage->addText(this->targetEntity->getResString());
				
				this->actionMessage->addSubject("Nebelfeld analysiert");
			}
						
			// If entity is an asteroidfield
			else if (this->targetEntity->getCode()=='a') {
				
				// Sending a message to the User with the data				
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[/b]hat das[b] ");
				this->actionMessage->addText(this->targetEntity->getCoords(),1);
				this->actionMessage->addText("[/b]um [b]");
				this->actionMessage->addText(this->f->getLandtimeString(),1);
				this->actionMessage->addText("[/b] analysiert.",2);
				this->actionMessage->addText(this->targetEntity->getResString());
				
				this->actionMessage->addSubject("Asteroidenfeld analysiert");
			}						
			
			// If entity is a gasplanet
			else if (this->targetEntity->getCode()=='p') {
				if (this->targetEntity->getTypeId()==7) {
					// Sending a message to the User with the data				
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]hat den[b] ");
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[/b]um [b]");
					this->actionMessage->addText(this->f->getLandtimeString(),1);
					this->actionMessage->addText("[/b] analysiert.",2);
					this->actionMessage->addText(this->targetEntity->getResString());
					
					this->actionMessage->addSubject("Gasplanet analysiert");
				}
				// If planet is not a gasplanet
				else {
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]versuchte, das Ziel zu analysieren. Konnte jedoch [b]keinen Gasplaneten [/b]vorfinden.");
					
					this->actionMessage->addSubject("Analyseversuch gescheitert");
					
					this->actionLog->addText("Action failed: entity error");
				}
			}
									
			// If non of the possible entitys was there
			else {
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[/b] versuchte, das Ziel zu analysieren. Konnte jedoch nur die unendlichen Weiten des leeren Raumes bestaunen.");
				
				this->actionMessage->addSubject("Analyseversuch gescheitert");
				
				this->actionLog->addText("Action failed: entity error");
			}
		}
		
		// If no ship with the action was in the fleet 
		else {
			this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("versuchte das Ziel zu analysieren. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
			
			this->actionMessage->addSubject("Analyseversuch gescheitert");
			
			this->actionLog->addText("Action failed: Ship error");
		}
		
		this->f->setReturn();
	}
}
