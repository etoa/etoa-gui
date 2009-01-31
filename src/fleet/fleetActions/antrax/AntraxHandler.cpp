
#include "AntraxHandler.h"

namespace antrax
{
	void AntraxHandler::update()
	{
	
		/**
		* Fleet-Action: Antrax-Attack
		*/
		
		/** Initialize data **/
		Config &config = Config::instance();
		
		BattleHandler *bh = new BattleHandler(this->actionMessage);
		bh->battle(this->f,this->targetEntity,this->actionLog);
		
		this->actionMessage->addType((int)config.idget("SHIP_WAR_MSG_CAT_ID"));
		
		// Antrax the planet
		if (bh->returnV) {
			
			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->tLevel = this->f->fleetUser->getTechLevel("Gifttechnologie");
				this->shipCnt = this->f->getActionCount();
				
				// Calculate the chance 
				this->one = rand() % 101;
				this->two = config.nget("antrax_action",0) + ceil(this->shipCnt/10000.0) + this->tLevel * 5 + this->f->getSpecialShipBonusAntrax() * 100;
				
				if (this->one <= this->two) {
					// Calculate the damage percentage (Max. 90%) 
					this->temp = (int)std::min((10 + this->tLevel * 3),(int)config.nget("antrax_action",1));
					this->fak = rand() % temp;
					this->fak += (int)ceil(this->shipCnt/10000.0);
					
					// Calculate the real damage 
					this->people = this->targetEntity->removeResPeople(round(this->targetEntity->getResPeople() * this->fak / 100));
					this->food = this->targetEntity->removeResFood(round(this->targetEntity->getResFood() * this->fak / 100));
					
					/// Send message to the entity and the fleet user 
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]einen Antraxangriff auf den Planeten[b]",1);
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[/b]verübt es starben dabei ");
					this->actionMessage->addText(etoa::nf(etoa::d2s(this->people)));
					this->actionMessage->addText(" Bewohner und ");
					this->actionMessage->addText(etoa::nf(etoa::d2s(this->food)));
					this->actionMessage->addText(" t Nahrung wurden verbrann.");
					
					this->actionMessage->addSubject("Antraxangriff");
					this->actionMessage->addUserId(this->targetEntity->getUserId());
					
					//Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
					
					this->f->deleteActionShip(1);
				}
					// if antrax failed 
				else  {
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]hat erfolglos einen Antraxangriff auf den Planeten[b]",1);
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[/b]verübt.");
					
					this->actionMessage->addSubject("Antraxangriff erfolglos");
					this->actionMessage->addUserId(this->targetEntity->getUserId());
				}
			}
			// If no ship with the action was in the fleet 
			else {
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[/b]versuchte eine Antraxangriff auszuführen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
				
				this->actionMessage->addSubject("Antraxangriff gescheitert");
				
				this->actionLog->addText("Action failed: Ship error");
			}
		}
		else
			this->actionMessage->dontSend();
		
		this->f->setReturn();
		delete bh;
	}
}
