
#include "SpyHandler.h"

namespace spy
{
	void SpyHandler::update()
	{
	
		/**
		* Fleet-Action: Spy
		*/
		
		Config &config = Config::instance();
		
		// Load tech levels first agressor, needs a value higher then 0 for one of them, cause /0 
		this->spyLevelAtt = this->f->fleetUser->getTechLevel((unsigned int)config.idget("SPY_TECH_ID")) + 1e-2 + this->f->fleetUser->getSpecialist()->getSpecialistSpyLevel();
		this->tarnLevelAtt = this->f->fleetUser->getTechLevel((unsigned int)config.idget("TARN_TECH_ID")) + this->f->fleetUser->getSpecialist()->getSpecialistTarnLevel();

		// Then load the tech levels of the victim
		this->spyLevelDef = this->targetEntity->getUser()->getTechLevel((unsigned int)config.idget("SPY_TECH_ID"));
		
		std::cout << "TODO: spy specialist makes trouble (sigsegv when fetching level)"<<std::endl;
		//this->spyLevelDef += this->targetEntity->getUser()->getSpecialist()->getSpecialistSpyLevel();
		this->tarnLevelDef = this->targetEntity->getUser()->getTechLevel((unsigned int)config.idget("TARN_TECH_ID"));
		//this->tarnLevelDef = + this->targetEntity->getUser()->getSpecialist()->getSpecialistTarnLevel();
		
		
		// Load spy ships agressor 
		this->spyShipsAtt = this->f->getActionCount();
		
		// Load spy ships defender or sometimes victim 
		this->spyShipsDef = this->targetEntity->getSpyCount();
		
		// If there are some spy ships in the fleet 
		if (spyShipsAtt) {
			// Calculate the defense 
			this->spyDefense1 = std::max(0.0,(this->spyLevelDef / (this->spyLevelAtt + this->tarnLevelAtt) * config.idget("SPY_DEFENSE_FACTOR_TECH")));
			this->spyDefense2 = std::max(0.0,((this->spyShipsDef / this->spyShipsAtt) * config.idget("SPY_DEFENSE_FACTOR_SHIPS")));
			this->spyDefense = std::min(this->spyDefense1 + this->spyDefense2,config.idget("SPY_DEFENSE_MAX"));
			
			//reports init
			SpyReport *fleetReport = new SpyReport(this->f->getUserId(),
												   this->f->getEntityTo(),
												   this->f->getEntityFrom(),
												   this->f->getLandtime(),
												   this->f->getId(),
												   this->targetEntity->getUserId());
			
			SpyReport *entityReport = new SpyReport(this->targetEntity->getUserId(),
													this->f->getEntityTo(),
													this->f->getEntityFrom(),
													this->f->getLandtime(),
													this->f->getId(),
													this->f->getUserId());
			
			this->roll = rand() % 101;
			
			this->actionLog->addText(etoa::d2s(this->spyDefense) + " >= " + etoa::d2s(this->roll));
			if (this->roll >= this->spyDefense) {
				// Calculate stealth bonus 
				this->tarnDefense = std::max(0.0,std::min((this->tarnLevelDef / this->spyLevelAtt * config.idget("SPY_DEFENSE_FACTOR_TARN")),config.idget("SPY_DEFENSE_MAX")));
				
				// If the spy tech level is high enough show the buildings 
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_BUILDINGS") && (rand() % 101) > this->tarnDefense)
					fleetReport->setBuildings(this->targetEntity->getBuildingString());
				
				// Same with the technologies 
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_RESEARCH") && (rand() % 101) > this->tarnDefense)
					fleetReport->setTechnologies(this->targetEntity->getUser()->getTechString());
				
				// Next to go flag for support ships
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_SUPPORT") && (rand() % 101) > this->tarnDefense)
					this->support = true;
				else
					this->support = false;
				
				// Next to go are the ships 
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_SHIPS") && (rand() % 101) > this->tarnDefense)
					fleetReport->setShips(this->targetEntity->getShipString(this->support));
		
				// .., the defense, ... 
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_DEFENSE") && (rand() % 101) > this->tarnDefense)
					fleetReport->setDefense(this->targetEntity->getDefString());
		
				// and at last the resources on the planet
                int spy=1;
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_RESSOURCEN") && (rand() % 101) > this->tarnDefense)
					fleetReport->setRes(floor(this->targetEntity->getResMetal(spy)),
										floor(this->targetEntity->getResCrystal(spy)),
										floor(this->targetEntity->getResPlastic(spy)),
										floor(this->targetEntity->getResFuel(spy)),
										floor(this->targetEntity->getResFood(spy)),
										floor(this->targetEntity->getResPeople()));
				
				fleetReport->setSpydefense(round(this->spyDefense));
				fleetReport->setCoverage(round(this->tarnDefense));
				
				entityReport->setSpydefense(round(this->spyDefense));
				entityReport->setCoverage(round(this->tarnDefense));
				
				fleetReport->setSubtype("spy");
				entityReport->setSubtype("surveillance");
			}
			// if the mission failed 
			else {
				fleetReport->setSubtype("spyfailed");
				entityReport->setSubtype("surveillancefailed");
			}
			
			fleetReport->setSpydefense(round(this->spyDefense));
			fleetReport->setCoverage(round(this->tarnDefense));
			
			entityReport->setSpydefense(round(this->spyDefense));
			entityReport->setCoverage(round(this->tarnDefense));
			
			delete fleetReport;
			delete entityReport;
		}
		// if there was no spy ship in the fleet 
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
