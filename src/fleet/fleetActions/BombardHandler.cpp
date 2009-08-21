
#include "BombardHandler.h"


namespace bombard
{
	void BombardHandler::update()
	{
	
		/**
		* Fleet-Action: Bombard
		*/
		
		Config &config = Config::instance();
		
		BattleHandler *bh = new BattleHandler();
		bh->battle(this->f,this->targetEntity,this->actionLog);

		// Bombard the planet
		if (bh->returnV==1) {
			
			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->tLevel = this->f->fleetUser->getTechLevel((unsigned int)config.idget("BOMB_TECH_ID"));
				this->shipCnt = this->f->getActionCount(true);
				
				// 10% + Boni, for success
				this->one = rand() % 101;
				this->two = config.nget("ship_bomb_factor",1) + (config.nget("ship_bomb_factor",0) * this->tLevel + ceil(this->shipCnt / 10000) + this->f->getSpecialShipBonusBuildDestroy() * 100);
				
				//Battlereport
				BattleReport *bombard = new BattleReport(this->f->getUserId(),
														 this->targetEntity->getUserId(),
														 this->f->getEntityTo(),
														 this->f->getEntityFrom(),
														 this->f->getLandtime(),
														 this->f->getId());
				bombard->addUser(this->targetEntity->getUserId());
				
				if (this->one < this->two) 
				{
					// level the building down, at least one level 
					this->bLevel = (short)ceil(this->shipCnt/2500.0);
					
					std::string actionString = this->targetEntity->bombBuilding(this->bLevel);
					
					if (actionString.length()) {
						bombard->setContent(actionString);
						bombard->setSubtype("bombard");
						
						this->actionLog->addText("Action succeed: " + etoa::d2s(this->one) + " < " + etoa::d2s(this->two));
						
						etoa::addSpecialiBattle(this->f->getUserId(),"Spezialaktion");
						
						this->f->deleteActionShip(1);
					}
					// If bombard failed (no building)
					else
						bombard->setSubtype("bombardfailed");
				} 
					// if bombard failed
				else
					bombard->setSubtype("bombardfailed");
				delete bombard;
			}
			// If no ship with the action was in the fleet 
			else {
				OtherReport *report = new OtherReport(this->f->getUserId(),
													this->f->getEntityTo(),
													this->f->getEntityFrom(),
													this->f->getLandtime(),
													this->f->getId(),
													this->f->getAction());
				report->setSubtype("actionfailed");

				delete report;
				
				this->actionLog->addText("Action failed: Ship error");
			}
		}
		else 
		
		this->f->setReturn();
		delete bh;
	}
}
