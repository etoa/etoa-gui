
#include "ColonializeHandler.h"

namespace colonialize
{
	void ColonializeHandler::update()
	{
	
		/**
		* Fleet-Action: Colonialize
		*/
		Config &config = Config::instance();
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
		
			if (this->targetEntity->getUserId()) {
			
				if(this->targetEntity->getUserId() == this->f->getUserId()) {
					
					this->actionMessage->addText("Die Flotte hat folgendes Ziel erreicht:",1);
					this->actionMessage->addText("[b]Planet:[/b] ");
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!");
					
					this->actionMessage->addSubject("Flotte angekommen");
					fleetLand(1);
				}
				// If the planet belongs to en other user, return the fleet back home
				else {
					// Send a message to the user
					this->actionMessage->addText("Die Flotte kann den Planeten nicht kolonialisieren, da er bereits von einem anderen Volk kolonialisiert wurde!",1);
					this->actionMessage->addSubject("Landung nicht möglich");
				}
			}
			// if the planet has not yet a user
			else {
				if (this->f->fleetUser->getPlanetsCount() >= (int)config.nget("user_max_planets",0)) {
					this->actionMessage->addText("Die Flotte kann den Planeten nicht kolonialisieren, da die maximale Zahl an Planeten auf denen du regieren darfst, bereits erreicht worden ist!",1);
					this->actionMessage->addSubject("Landung nicht möglich");
				}
				// if up to now everything is fine, let's colonialize the planet
				else {
					// reset the planet
					this->targetEntity->resetEntity(this->f->getUserId());
						
					this->actionMessage->addText("Die Flotte hat folgendes Ziel erreicht:",1);
					this->actionMessage->addText("[b]Planet:[/b] ");
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[b]Bericht:[/b] Die Flotte hat eine neue Kolonie errichtet! Dabei wurde ein Besiedlungsschiff verbraucht.");
					
					this->actionMessage->addSubject("Planet kolonialisiert");
					
					this->f->deleteActionShip(1);
					
					// Land the fleet and delete one ship (action colonialize)
					fleetLand(1);
				}
			}
		}
		// If no ship with the action was in the fleet 
		else {
			this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
			this->actionMessage->addText(this->startEntity->getCoords(),1);
			this->actionMessage->addText("versuchte eine Kolonie zu errichten. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
			
			this->actionMessage->addSubject("Kolonisieren gescheitert");
			
			this->actionLog->addText("Action failed: Ship error");
		}
		this->f->setReturn();
	}
}
