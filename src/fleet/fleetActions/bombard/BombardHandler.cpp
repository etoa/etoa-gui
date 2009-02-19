
#include "BombardHandler.h"


namespace bombard
{
	void BombardHandler::update()
	{
	
		/**
		* Fleet-Action: Bombard
		*/
		Config &config = Config::instance();
		
		BattleHandler *bh = new BattleHandler(this->actionMessage);
		bh->battle(this->f,this->targetEntity,this->actionLog);
		
		this->actionMessage->addType((int)config.idget("SHIP_WAR_MSG_CAT_ID"));

		// Bombard the planet
		if (bh->returnV==1) {
			
			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->tLevel = this->f->fleetUser->getTechLevel("Bombentechnik");
				this->shipCnt = this->f->getActionCount(true);
				
				// 10% + Bonis, dass Bombardierung erfolgreich
				this->one = rand() % 101;
				this->two = config.nget("ship_bomb_factor",1) + (config.nget("ship_bomb_factor",0) * this->tLevel + ceil(this->shipCnt / 10000) + this->f->getSpecialShipBonusBuildDestroy() * 100);
				
				if (this->one < this->two) {
					// level the building down, at least one level 
					this->bLevel = ceil(this->shipCnt/2500.0);
					
					std::string actionString = this->targetEntity->bombBuilding(this->bLevel);
					
					if (actionString.length()) {
						
						this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
						this->actionMessage->addText(this->startEntity->getCoords(),1);
						this->actionMessage->addText(actionString,1);
						
						this->actionMessage->addSubject("Gebäude bombardiert");
						this->actionMessage->addUserId(this->targetEntity->getUserId());
						
						this->actionLog->addText("Action succeed: " + etoa::d2s(this->one) + " < " + etoa::d2s(this->two));
						
						etoa::addSpecialiBattle(this->f->getUserId(),"Spezialaktion");
						
						this->f->deleteActionShip(1);
					}
					// If bombard failed 
					else  {
						this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
						this->actionMessage->addText(this->startEntity->getCoords(),1);
						this->actionMessage->addText("[/b]hat erfolglos versucht ein Gebäude des Planeten [b]",1);
						this->actionMessage->addText(this->targetEntity->getCoords(),1);
						this->actionMessage->addText("[/b]zu bombardieren.");
						
						this->actionMessage->addSubject("Bombardierung erfolglos");
						this->actionMessage->addUserId(this->targetEntity->getUserId());
					}
				} 
					// if stealing a tech failed
				else  {
					this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
					this->actionMessage->addText(this->startEntity->getCoords(),1);
					this->actionMessage->addText("[/b]hat erfolglos versucht ein Gebäude des Planeten[b]",1);
					this->actionMessage->addText(this->targetEntity->getCoords(),1);
					this->actionMessage->addText("[/b]zu bombardieren.");
					
					this->actionMessage->addSubject("Bombardierung erfolglos");
					this->actionMessage->addUserId(this->targetEntity->getUserId());
				}
			}
			// If no ship with the action was in the fleet 
			else {
				this->actionMessage->addText("Eine Flotte vom Planeten [b]",1);
				this->actionMessage->addText(this->startEntity->getCoords(),1);
				this->actionMessage->addText("[/b]versuchte eine Bombardierung auszuführen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!");
				
				this->actionMessage->addSubject("Bombardierung gescheitert");
				
				this->actionLog->addText("Action failed: Ship error");
			}
		}
		else 
			this->actionMessage->dontSend();
		
		this->f->setReturn();
		delete bh;
	}
}
