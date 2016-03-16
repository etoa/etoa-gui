
#include "AnalyzeHandler.h"

namespace analyze
{
	void AnalyzeHandler::update()
	{
	
		/**
		* Fleet action: Analyze
		*/
		Config &config = Config::instance();
		// Precheck action==possible?
		if (this->f->actionIsAllowed()) {
			
			SpyReport *report = new SpyReport(this->f->getUserId(),
											  this->f->getEntityTo(),
											  this->f->getEntityFrom(),
											  this->f->getLandtime(),
											  this->f->getId());
			// If entity is a analyzable
			if (this->targetEntity->getCode()=='n'
				|| this->targetEntity->getCode()=='a'
				|| (this->targetEntity->getCode()=='p' && this->targetEntity->getTypeId()==config.nget("gasplanet",0))) {
				
				report->setRes(floor(this->targetEntity->getResMetal()),
							   floor(this->targetEntity->getResCrystal()),
							   floor(this->targetEntity->getResPlastic()),
							   floor(this->targetEntity->getResFuel()),
							   floor(this->targetEntity->getResFood()),
							   floor(this->targetEntity->getResPeople()));
				
				report->setSubtype("analyze");
			}
									
			// If non of the possible entitys was there
			else
				report->setSubtype("analyzefaild");
			
			delete report;
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
		}
		
		this->f->setReturn();
	}
}
