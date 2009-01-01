
#include "GasHandler.h"

namespace gas
{
	void GasHandler::update()
	{
	
		/**
		* Fleet-Action: Gas collect on gas planet
		*/
		
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
		
			// Check if there is a field
			if (this->targetEntity->getCode()=='p' && this->targetEntity->getTypeId()==config.nget("gasplanet",0)) {
				
				this->one = rand() % 101;
				this->two = (int)(config.nget("gascollect_action",0) * 100);
				
				// Ship were destroyed?
				if (this->one  < this->two)	{
					int percent = 100 - rand() % (int)(config.nget("gascollect_action",1) * 100);
					this->f->setPercentSurvive(percent);
				}
				
				
				if (this->f->actionIsAllowed()) {
					this->sum = 0;
					
					this->fuel = 1000 + (rand() % (int)(this->f->getActionCapacity() - 999));
					this->sum +=this->f->addFuel(this->targetEntity->removeResFuel(std::min(this->fuel,this->targetEntity->getResFuel())));
					
					this->actionMessage->addText("[b]GASSAUGER-RAPPORT[/b]",2);
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]hat den[b]",1);
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[/b]um [b]");
					this->actionMessage->addText(this->f->getLandtimeString(),1);
					this->actionMessage->addText("[/b]erkundet und Gas gesaugt.");
					this->actionMessage->addText(this->f->getResCollectedString(),1);
					this->actionMessage->addText(this->f->getDestroyedShipString("\n\nAufgrund starker Wasserstoffexplosionen sind einige deiner Schiffe zerst&ouml;rt worden:\n\n"));
					
					this->actionMessage->addSubject("Gas gesaugt");
					
					// Save the collected resources
					this->fleetUser->addCollectedNebula(this->sum);
				
				}
				// if there are no nebula collecter in the fleet anymore
				else {
					// Send a message to the user
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("wurde bei einem Gasplaneten vollkommen zerstört.");
										
					this->actionMessage->addSubject("Flotte zerstört");
					
					this->actionLog->addText("Action failed: Shot error");
				}
			}
			// If the asteroid field isnt there anymore
			else {
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[/b]hatte sich auf dem Weg zu einem Gasplaneten, wohl gründlich verflogen und kehrte auf direktem Weg zurück nach Hause.");				
				this->actionMessage->addSubject("Gassaugen gescheitert");
				
				this->actionLog->addText("Action failed: entity error");
			}
		}
		
		// If there isnt any asteroid colecter in the fleet 
		else {
			this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("versuchte Gas zu saugen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
			
			this->actionMessage->addSubject("Gassaugen gescheitert");
			
			this->actionLog->addText("Action failed: Ship error");
		}
		
		this->f->setReturn();
	}
}
