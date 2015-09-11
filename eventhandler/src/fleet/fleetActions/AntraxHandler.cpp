
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
		
		BattleHandler *bh = new BattleHandler();
		bh->battle(this->f,this->targetEntity,this->actionLog);
		
		// Antrax the planet
		if (bh->returnV==1) {
			
			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->tLevel = this->f->fleetUser->getTechLevel((unsigned int)config.idget("POISON_TECH_ID"));
				this->shipCnt = this->f->getActionCount();
				
				// Calculate the chance 
				this->one = rand() % 101;
				this->two = config.nget("antrax_action",0) + ceil(this->shipCnt/10000.0) + this->tLevel * 5 + this->f->getSpecialShipBonusAntrax() * 100;
				
				//Battlereport
				BattleReport *antrax = new BattleReport(this->f->getUserId(),
														 this->targetEntity->getUserId(),
														 this->f->getEntityTo(),
														 this->f->getEntityFrom(),
														 this->f->getLandtime(),
														 this->f->getId());
				antrax->addUser(this->targetEntity->getUserId());
														
				if (this->one < this->two) {
					// Calculate the damage percentage (Max. 90%) 
					this->temp = (int)std::min((10 + this->tLevel * 3),(int)config.nget("antrax_action",1));
					this->fak = rand() % temp;
					this->fak += (int)ceil(this->shipCnt/10000.0);
					
					// Calculate the real damage 
					this->people = this->targetEntity->removeResPeople(round(this->targetEntity->getResPeople() * this->fak / 100));
					this->food = this->targetEntity->removeResFood(round(this->targetEntity->getResFood() * this->fak / 100));
					
					// Add Reportdata
					antrax->setSubtype("antrax");
					antrax->setRes(0,0,0,0,this->food,this->people);
					
					this->actionLog->addText("Action succeed: " + etoa::d2s(this->one) + " < " + etoa::d2s(this->two));
					
					etoa::addSpecialiBattle(this->f->getUserId(),"Spezialaktion");
					
					this->f->deleteActionShip(1);
				}
					// if antrax failed 
				else
					antrax->setSubtype("antraxfailed");
				delete antrax;
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
		
		this->f->setReturn();
		delete bh;
	}
}
