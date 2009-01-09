
#include "AsteroidHandler.h"

namespace asteroid
{
	void AsteroidHandler::update()
	{
	
		/**
		* Fleet-Action: Collect asteroids
		*/ 
		
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			// Check if there is a field
			if (this->targetEntity->getCode()=='a' && this->targetEntity->getResSum()>0) {
				
				this->one = rand() % 101;
				this->two = (int)(config.nget("asteroid_action",0));
				
				// Ship were destroyed?
				if (this->one  < this->two)	{
					int percent = 100 - rand() % (int)(config.nget("asteroid_action",1));
					this->f->setPercentSurvive(percent/100.0);
				}
				
				if (this->f->actionIsAllowed()) {
					this->sum = 0;
					
					this->asteroid = config.nget("asteroid_action",2) + (rand() % (int)(this->f->getActionCapacity()/3 - config.nget("asteroid_action",2) + 1));
					this->sum +=this->f->addMetal(this->targetEntity->removeResMetal(std::min(this->asteroid,this->targetEntity->getResMetal())));
					
					this->asteroid = config.nget("asteroid_action",2) + (rand() % (int)(this->f->getActionCapacity()/3 - config.nget("asteroid_action",2) + 1));
					this->sum +=this->f->addCrystal(this->targetEntity->removeResCrystal(std::min(this->asteroid,this->targetEntity->getResCrystal())));
					
					this->asteroid = config.nget("asteroid_action",2) + (rand() % (int)(this->f->getActionCapacity()/3 - config.nget("asteroid_action",2) + 1));
					this->sum +=this->f->addPlastic(this->targetEntity->removeResPlastic(std::min(this->asteroid,this->targetEntity->getResPlastic())));
					
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]hat das[b]",1);
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[/b]um [b]");
					this->actionMessage->addText(this->f->getLandtimeString(),1);
					this->actionMessage->addText("[/b]erreicht und Rohstoffe gesammelt.");
					this->actionMessage->addText(this->f->getResCollectedString(),1);
					this->actionMessage->addText(this->f->getDestroyedShipString("\n\nAufrund einer Kolision mit einem Asteroiden sind einige deiner Schiffe zerst&ouml;rt worden:\n\n"),1);
					
					this->actionMessage->addSubject("Asteroiden gesammelt");
					
					// Save the collected resources
					this->f->fleetUser->addCollectedAsteroid(this->sum);
				}
				
				// If there arent any asteroid collecter anymore
				else {
					// Send a message to the user
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("wurde bei einem Asteroidenfeld abgeschossen.");
					
					this->actionMessage->addSubject("Flotte abgeschossen");
					
					this->actionLog->addText("Action failed: Shot error");
				}
			}
			// If the asteroid field isnt there anymore
			else {
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[/b]versuchte, Asteroiden zu sammeln, doch war kein Asteroidenfeld mehr vorhanden und so machte sich die Crew auf den Weg nach Hause.");
				
				this->actionMessage->addSubject("Asteroidensammeln gescheitert");
				
				this->actionLog->addText("Action failed: entity error");
			}
		}
		
		// If there isnt any asteroid colecter in the fleet 
		else {
			this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText(" versuchte, Asteroiden zu sammeln. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
			
			this->actionMessage->addSubject("Asteroidensammeln gescheitert");
			
			this->actionLog->addText("Action failed: Ship error");
		}
		
		this->f->setReturn();
	}
}
