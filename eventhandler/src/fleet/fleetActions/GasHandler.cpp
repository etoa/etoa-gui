
#include "GasHandler.h"

namespace gas
{
	void GasHandler::update()
	{

		/**
		* Fleet-Action: Gas collect on gas planet
		*/

		Config &config = Config::instance();

		OtherReport *report = new OtherReport(this->f->getUserId(),
											  this->f->getEntityTo(),
											  this->f->getEntityFrom(),
											  this->f->getLandtime(),
											  this->f->getId(),
											  this->f->getAction());
		report->setStatus(this->f->getStatus());

		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {

			// Check if there is a field
			if (this->targetEntity->getCode()=='p' && this->targetEntity->getTypeId()==config.nget("gasplanet",0)) {
				report->setSubtype("collectfuel");

				this->one = rand() % 101;
				this->two = (int)config.nget("gascollect_action",0);

				// Ship were destroyed?
				if (this->one  < this->two)	{
					int percent = 100 - rand() % (int)(config.nget("gascollect_action",1));

					this->f->setPercentSurvive(percent/100.0);
				}

				report->setShips(this->f->getDestroyedShipString());

				if (this->f->actionIsAllowed()) {
					this->sum = 0;

					this->fuel = (int)config.nget("gascollect_action",2) + (rand() % (int)(this->f->getActionCapacity() - (int)config.nget("gascollect_action",2)));
					this->sum +=this->f->addFuel(this->targetEntity->removeResFuel(std::min(this->fuel,this->targetEntity->getResFuel())));

					report->setRes(0,
								   0,
								   0,
								   sum);

					// Save the collected resources
					this->f->fleetUser->addCollectedNebula(this->sum);

				}
				// if there are no nebula collecter in the fleet anymore
				else {
					report->setSubtype("actionshot");
					this->actionLog->addText("Action failed: Shot error");
				}
			}
			// If the gasplanet field isnt there anymore
			else {
				report->setSubtype("collectfuelfailed");

				this->actionLog->addText("Action failed: entity error");
			}
		}

		// If there isnt any asteroid colecter in the fleet
		else {
			report->setSubtype("actionfailed");

			this->actionLog->addText("Action failed: Ship error");
		}

		delete report;
		this->f->setReturn();
	}
}
