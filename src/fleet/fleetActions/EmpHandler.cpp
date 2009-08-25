
#include "EmpHandler.h"

namespace emp
{
	void EmpHandler::update()
	{
	
		/**
		* Fleet-Action: EMP-Attack
		*/
		
		Config &config = Config::instance();

		// Calculate the battle
		BattleHandler *bh = new BattleHandler();
		bh->battle(this->f,this->targetEntity,this->actionLog);
		
		// If the attacker is the winner, deactivade a building
		if (bh->returnV==1) {
			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->shipCnt = this->f->getActionCount();
				this->tLevel = this->f->fleetUser->getTechLevel((unsigned int)config.idget("EMP_TECH_ID"));
				
				// Calculate the possibility
				this->one = rand() % 101;
				this->two = 10 + ceil(this->shipCnt/10000.0) + this->tLevel * 5 + this->f->getSpecialShipBonusEMP() * 100;
				
				//Battlereport
				BattleReport *emp = new BattleReport(this->f->getUserId(),
														 this->targetEntity->getUserId(),
														 this->f->getEntityTo(),
														 this->f->getEntityFrom(),
														 this->f->getLandtime(),
														 this->f->getId());
				emp->addUser(this->targetEntity->getUserId());
				
				if (this->one < this->two) {
					
					// Calculate the damage
					this->h = rand() % (10 + this->tLevel + 1); //ToDo Add shipCnt
					
					std::string actionString = this->targetEntity->empBuilding(this->h);
					
					if (actionString.length()) {
						emp->setContent(actionString);
						emp->setSubtype("emp");
						
						this->actionLog->addText("Action succeed: " + etoa::d2s(this->one) + " < " + etoa::d2s(this->two));
						
						etoa::addSpecialiBattle(this->f->getUserId(),"Spezialaktion");
						
						this->f->deleteActionShip(1);
					}
					// If there exists no building to deactivade, send a message to the planet and the fleet user
					else {
						emp->setContent("1");
						emp->setSubtype("empfailed");
					}
				}
				
				// If the deactivation failed, send a message to the planet and the fleet user
				else
					emp->setSubtype("empfailed");
				delete emp;
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
