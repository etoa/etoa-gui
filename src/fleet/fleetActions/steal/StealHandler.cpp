
#include "StealHandler.h"

namespace steal
{
	void StealHandler::update()
	{
	
		/**
		* Fleet-Action: Spy-Attack (Steal technology)
		*/

		// Initialize data
		Config &config = Config::instance();
				
		BattleHandler *bh = new BattleHandler(this->actionMessage);
		bh->battle(this->f,this->targetEntity,this->actionLog);
		
		this->actionMessage->addType((int)config.idget("SHIP_WAR_MSG_CAT_ID"));

		// Steal a tech
		if (bh->returnV==1) {
			bh->returnFleet = true;
			
			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->tLevelAtt = (int)this->f->fleetUser->getTechLevel("Spionagetechnik") + (int)this->f->fleetUser->getSpecialist()->getSpecialistSpyLevel();
				this->tLevelDef = (int)this->targetEntity->getUser()->getTechLevel("Spionagetechnik") + (int)this->f->fleetUser->getSpecialist()->getSpecialistTarnLevel();
				this->shipCnt = this->f->getActionCount();
				
				// Calculate the chance
				this->one = rand() % 1;
				this->two = std::min(config.nget("spyattack_action",2),std::max(config.nget("spyattack_action",1),(config.nget("spyattack_action",0) +this->tLevelAtt - this->tLevelDef + ceil(this->shipCnt/10000.0)+ this->f->getSpecialShipBonusForsteal() * 100)));
				
				if (this->one < this->two) {
				
					std::string actionString = this->f->fleetUser->stealTech(this->targetEntity->getUser());
					
					//if there is a tech
					if (actionString.length()) {
						this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
						this->actionMessage->addText(this->startEntity->getCoords(),1);
						this->actionMessage->addText("[/b] hat erfolgreich einen Spionageangriff durchgeführt und erfuhr so die Geheimnisse der Forschung ");
						this->actionMessage->addText(actionString);
						
						this->actionMessage->addSubject("Spionageangriff");
						this->actionMessage->addUserId(this->targetEntity->getUserId());
						
						this->actionLog->addText("Action succeed: " + etoa::d2s(this->one) + " < " + etoa::d2s(this->two));
						
						etoa::addSpecialiBattle(this->f->getUserId(),"Spezialaktion");
						
						this->f->deleteActionShip(1);
					}
					
					// if stealing a tech failed
					else  {
						this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
						this->actionMessage->addText(this->startEntity->getCoords(),1);
						this->actionMessage->addText("[/b]hat erfolglos einen Spionageangriff auf den Planeten[b]",1);
						this->actionMessage->addText(this->targetEntity->getCoords(),1);
						this->actionMessage->addText("[/b]verübt.");
						
						this->actionMessage->addSubject("Spionageangriff erfolglos");
						this->actionMessage->addUserId(this->targetEntity->getUserId());
						
						this->actionLog->addText("Action failed: Tech error");
					}
				} 
					// if stealing a tech failed
				else  {
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]hat erfolglos einen Spionageangriff auf den Planeten[b]",1);
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[/b]verübt.");
					
					this->actionMessage->addSubject("Spionageangriff erfolglos");
					this->actionMessage->addUserId(this->targetEntity->getUserId());
				}
			}
			// If no ship with the action was in the fleet 
			else {
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[/b]versuchte eine Spionageangriff auszuführen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
				
				this->actionMessage->addSubject("Spionageangriff gescheitert");
				
				this->actionLog->addText("Action failed: Ship error");
			}
		}
		else
			this->actionMessage->dontSend();
		
		this->f->setReturn();
		delete bh;
	}
}
