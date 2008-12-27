
#include "WreckageHandler.h"

namespace wreckage
{
	void WreckageHandler::update()
	{
		/**
		* Fleet-Action: Collect wreckage/debris field
		*/ 
		
		Config &config = Config::instance();
		
		this->actionMessage->addType((int)config.idget("SHIP_MISC_MSG_CAT_ID"));
		
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			
			this->actionMessage->addText("[b]TR&Uuml;MMERSAMMLER-RAPPORT[/b]\n");
			this->actionMessage->addText("Eine Flotte vom Planeten [b]");
			this->actionMessage->addText(this->startEntity->getCoords());
			this->actionMessage->addText("[/b]hat das Tr&uuml;mmerfeld bei[b]");
			this->actionMessage->addText(this->targetEntity->getCoords());
			this->actionMessage->addText("[/b]um [b]");
			this->actionMessage->addText(f->getLandtimeString());
			
			this->actionMessage->addSubject("Tr&uuml;mmer gesammelt");
			
			// Check if there is a field
			if (this->targetEntity->getWfSum()>0) {
				
				this->sum = this->targetEntity->getWfSum();
				
				// Calculate the collected resources
				if (this->f->getCapacity() <= this->targetEntity->getWfSum()) {
					this->percent = this->f->getCapacity() / this->targetEntity->getWfSum();
					this->metal = this->targetEntity->removeWfMetal(this->f->addMetal(this->targetEntity->getWfMetal() * this->percent));
					this->crystal = this->targetEntity->removeWfCrystal(this->f->addCrystal(this->targetEntity->getWfCrystal() * this->percent));
					this->plastic = this->targetEntity->removeWfPlastic(this->f->addPlastic(this->targetEntity->getWfPlastic() * this->percent));
					this->sum = this->metal + this->crystal + this->plastic;
				}
				else {
					this->metal = this->targetEntity->removeWfMetal(this->f->addMetal(this->targetEntity->getWfMetal()));
					this->crystal = this->targetEntity->removeWfCrystal(this->f->addCrystal(this->targetEntity->getWfCrystal()));
					this->plastic = this->targetEntity->removeWfPlastic(this->f->addPlastic(this->targetEntity->getWfPlastic()));
				}
				
				this->actionMessage->addText("[/b] erreicht und Tr&uuml;mmer gesammelt.");
				this->actionMessage->addText("\n[b]ROHSTOFFE:[/b]\n");
				this->actionMessage->addText("Titan: " + functions::nf(functions::d2s(this->metal)));
				this->actionMessage->addText("Silitium: " + functions::nf(functions::d2s(this->crystal)));
				this->actionMessage->addText("PVC: " + functions::nf(functions::d2s(this->plastic)));
				
				// Update collected resources for the userstatistic
				this->fleetUser->addCollectedWf(this->sum);
			}
			
			// If the field is empty
			else {
				
				// Send a message to the user
				this->actionMessage->addText("[/b] erreicht.");
				this->actionMessage->addText("\nEs wurden aber leider keine brauchbaren Trümmerteile mehr gefunden so dass die Flotte unverrichteter Dinge zurückkehren musste.");
			}
		}
		
		// If there is no wreckage collecter in the fleet
		else {
			this->actionMessage->addText("Eine Flotte vom Planeten [b]");
			this->actionMessage->addText(this->startEntity->getCoords());
			this->actionMessage->addText(" versuchte, Trümmer zu sammeln. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
			
			this->actionMessage->addSubject("Trümmersammeln gescheitert");
			
			this->actionLog->addText("Action failed: Ship error");
		}
		
		this->f->setReturn();
	}
}
