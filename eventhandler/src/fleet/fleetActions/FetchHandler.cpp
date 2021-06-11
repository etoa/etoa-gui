
#include "FetchHandler.h"

namespace fetch
{
	void FetchHandler::update()
	{

		/**
		* Fleet-Action: Fetch
		*/
		OtherReport *report = new OtherReport(this->f->getUserId(),
											  this->f->getEntityTo(),
											  this->f->getEntityFrom(),
											  this->f->getLandtime(),
											  this->f->getId(),
											  this->f->getAction());

		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {

			// Function is only allowed if the fleet user is the same as the planet user
			if (this->f->getUserId() == this->targetEntity->getUserId()) {
				report->setSubtype("fetch");

				report->setRes(this->f->addMetal(this->targetEntity->removeResMetal(std::min(this->f->getFetchMetal(), this->f->getCapacity()),false)),
							   this->f->addCrystal(this->targetEntity->removeResCrystal(std::min(this->f->getFetchCrystal(), this->f->getCapacity()),false)),
							   this->f->addPlastic(this->targetEntity->removeResPlastic(std::min(this->f->getFetchPlastic(), this->f->getCapacity()),false)),
							   this->f->addFuel(this->targetEntity->removeResFuel(std::min(this->f->getFetchFuel(), this->f->getCapacity()),false)),
							   this->f->addFood(this->targetEntity->removeResFood(std::min(this->f->getFetchFood(), this->f->getCapacity()),false)),
							   this->f->addPeople(this->targetEntity->removeResPeople(std::min(this->f->getFetchPeople(),this->f->getPeopleCapacity()))));
			}
			else {
				report->setSubtype("fetchfailed");

				this->actionLog->addText("Action failed: Planet error");
			}
		}
		else {
			report->setSubtype("actionfailed");

			this->actionLog->addText("Action failed: Ship error");
		}
		delete report;

		this->f->setReturn();
	}
}
