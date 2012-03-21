
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
				
		BattleHandler *bh = new BattleHandler();
		bh->battle(this->f,this->targetEntity,this->actionLog);

		// Steal a tech
		if (bh->returnV==1) {
			bh->returnFleet = true;
			
			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->tLevelAtt = (int)this->f->fleetUser->getTechLevel((unsigned int)config.idget("SPY_TECH_ID")) + (int)this->f->fleetUser->getSpecialist()->getSpecialistSpyLevel();
				this->tLevelDef = (int)this->targetEntity->getUser()->getTechLevel((unsigned int)config.idget("SPY_TECH_ID")) + (int)this->f->fleetUser->getSpecialist()->getSpecialistTarnLevel();
				this->shipCnt = this->f->getActionCount();
				
				// Calculate the chance
				double min = config.nget("spyattack_action",1); //minimum value
				double max = config.nget("spyattack_action",2); //maximum value
				double ini = config.nget("spyattack_action",0); //initial value
				this->one = rand() % 501; //Choose random number from 0 to 500
				/* Calculate a number
				 * > between min and max (the two min() and max() wrappers)
				 * > the MAXIMUM of
				 * - > ini plus difference between Att and Def tech levels
				 * -   plus the third root of the number of ships
				 * -   (third root seems to be about balanced)
				 * -   minus one (drawback of choosing third root and
				 * -   standard value of 3 for ini => average chance remains
				 * -   at about 4% with this -1)
				 * - > AND special ship bonus ratio applied to min,max
				 * === new TechSteal algorithm by river ===
				 */
				this->two = std::min(max, std::max(min, // wrappers
					std::max( // MAXIMUM
						(ini + this->tLevelAtt - this->tLevelDef // ini + diff(att,def)
							+ std::pow((double) this->shipCnt,(1.0/3.0)) -1), //shipCnt^(1/3) -1
						((max - min) * this->f->getSpecialShipBonusForsteal() + min) //linear ratio func
					)
				));
				this->two *= 5;
				this->two -= this->f->fleetUser->getSpyattackCount();
				
				BattleReport *spyattack = new BattleReport(this->f->getUserId(),
										this->targetEntity->getUserId(),
										this->f->getEntityTo(),
										this->f->getEntityFrom(),
										this->f->getLandtime(),
										this->f->getId());
				spyattack->addUser(this->targetEntity->getUserId());
				
				if (this->one < this->two) {
				
					std::string actionString = this->f->fleetUser->stealTech(this->targetEntity->getUser());
					
					//if there is a tech
					if (actionString.length()) {
						spyattack->setContent(actionString);
						spyattack->setSubtype("spyattack");
						
						this->actionLog->addText("Action succeed: " + etoa::d2s(this->one) + " < " + etoa::d2s(this->two));
						
						etoa::addSpecialiBattle(this->f->getUserId(),"Spezialaktion");
						
						this->f->deleteActionShip(1);
					}
					
					// if stealing a tech failed
					else  {
						spyattack->setSubtype("spyattackfailed");
						
						this->actionLog->addText("Action failed: Tech error");
					}
				} 
					// if stealing a tech failed
				else 
					spyattack->setSubtype("spyattackfailed");
				delete spyattack;
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
