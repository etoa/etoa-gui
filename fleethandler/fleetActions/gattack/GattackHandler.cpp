
#include "GattackHandler.h"

namespace gattack
{
	void GattackHandler::update()
	{
	
		/**
		* Fleet-Action: Gas-Attack
		*/
		Config &config = Config::instance();

		
		BattleHandler *bh = new BattleHandler(this->actionMessage);
		bh->battle(this->f,this->targetEntity,this->actionLog);
		
		this->actionMessage->addType((int)config.idget("SHIP_WAR_MSG_CAT_ID"));

		// gas-attack the planet
		if (bh->returnV==1) {
					
			// Precheck action==possible? 
			if (this->f->actionIsAllowed()) {
				this->shipCnt = this->f->getActionCount();
				this->tLevel = this->f->fleetUser->getTechLevel("Gifttechnologie");
				
				// Calculate the chance 
				this->one = rand() % 101;
				this->two = config.nget("gasattack_action",0) + ceil(this->shipCnt/10000.0) + this->tLevel * 5 + this->f->getSpecialShipBonusAntraxFood() * 100;
				
				if (this->one <= this->two) {
					// Calculate the damage percentage (Max. 95%) 
					this->temp = std::min((10 + this->tLevel * 3),(int)config.nget("gasattack_action",1));
					this->fak = rand() % temp;
					this->fak += ceil(this->shipCnt/10000.0);
					
					// Calculate dead planet people 
					this->people = this->targetEntity->removeResPeople(round(this->targetEntity->getResPeople() * this->fak / 100));
					
					// Send message to the entity and the fleet user 
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]einen Giftgasangriff auf den Planeten[b]",1);
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[/b]verübt es starben dabei ");
					this->actionMessage->addText(functions::nf(functions::d2s(this->people)));
					this->actionMessage->addText(" Bewohner.");
					
					this->actionMessage->addSubject("Giftgasangriff");
					this->actionMessage->addUserId(this->targetEntity->getUserId());
					
					//Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion"); //ToDo
					
					this->f->deleteActionShip(1);
					
				}
				else  {
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]hat erfolglos einen Giftgasangriff auf den Planeten",1);
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[/b]verübt.");
					
					this->actionMessage->addSubject("Giftgasangriff erfolglos");
					this->actionMessage->addUserId(this->targetEntity->getUserId());
				}
			}
			// If no ship with the action was in the fleet 
			else {
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[/b]versuchte eine Giftgasangriff auszuführen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
				
				this->actionMessage->addSubject("Giftgasangriff gescheitert");
				
				this->actionLog->addText("Action failed: Ship error");
			}
		}
		this->f->setReturn();
		delete bh;
	}
}
