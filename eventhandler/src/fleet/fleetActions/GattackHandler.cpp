
#include "GattackHandler.h"

namespace gattack
{
	void GattackHandler::update()
	{

		/**
		* Fleet-Action: Gas-Attack
		*/
		Config &config = Config::instance();


		BattleHandler *bh = new BattleHandler();
		bh->battle(this->f,this->targetEntity,this->actionLog);

		// gas-attack the planet
		if (bh->returnV==1) {

			// Precheck action==possible?
			if (this->f->actionIsAllowed()) {
				this->shipCnt = this->f->getActionCount();
				this->tLevel = this->f->fleetUser->getTechLevel((unsigned int)config.idget("POISON_TECH_ID"));

				// Calculate the chance
				this->one = rand() % 101;
				this->two = config.nget("gasattack_action",0) + ceil(this->shipCnt/10000.0) + this->tLevel * 5 + this->f->getSpecialShipBonusAntraxFood() * 100;

				//Battlereport
				BattleReport *gasattack = new BattleReport(this->f->getUserId(),
														 this->targetEntity->getUserId(),
														 this->f->getEntityTo(),
														 this->f->getEntityFrom(),
														 this->f->getLandtime(),
														 this->f->getId());
				gasattack->addUser(this->targetEntity->getUserId());

				if (this->one < this->two) {
					// Calculate the damage percentage (Max. 95%)
					this->temp = std::min((10 + this->tLevel * 3),(int)config.nget("gasattack_action",1));
					this->fak = rand() % temp;
					this->fak += (int)ceil(this->shipCnt/10000.0);

					// Calculate dead planet people
					this->people = this->targetEntity->removeResPeople(round(this->targetEntity->getResPeople() * this->fak / 100));

					// Add Reportdata
					gasattack->setSubtype("gasattack");
					gasattack->setRes(0,0,0,0,0,this->people);

					this->actionLog->addText("Action succeed: " + etoa::d2s(this->one) + " < " + etoa::d2s(this->two));

					etoa::addSpecialiBattle(this->f->getUserId(),"Spezialaktion");

					this->f->deleteActionShip(1);

				}
				else
					gasattack->setSubtype("gasattackfailed");
				delete gasattack;

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
