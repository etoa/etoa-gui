
#include "Planet.h"
	
	void Planet::loadData() {
		
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << "	planet_user_id, ";
		query << "	planet_user_main, ";
		query << "	planet_name, ";
		query << "	planet_type_id, ";
		query << "	planet_res_metal, ";
		query << "	planet_res_crystal, ";
		query << "	planet_res_fuel, ";
		query << "	planet_res_plastic, ";
		query << "	planet_res_food, ";
		query << "	planet_wf_metal, ";
		query << "	planet_wf_crystal, ";
		query << "	planet_wf_plastic, ";
		query << "	planet_people ";
		query << "FROM ";
		query << "	planets ";
		query << "WHERE ";
		query << "	id='" << this->getId() << "' ";
		query << "LIMIT 1;";
		mysqlpp::Result pRes = query.store();
		query.reset();

		if (pRes) {
			int pSize = pRes.size();
			
			if (pSize>0) {
				mysqlpp::Row pRow = pRes.at(0);
				
				this->userId = (int)pRow["planet_user_id"];
				this->planetUserMain = (bool)pRow["planet_user_main"];
				this->codeName = std::string(pRow["planet_name"]);
				this->planetType = (short)pRow["planet_type_id"];
				this->resMetal = (double)pRow["planet_res_metal"];
				this->resCrystal = (double)pRow["planet_res_crystal"];
				this->resPlastic = (double)pRow["planet_res_plastic"];
				this->resFuel = (double)pRow["planet_res_fuel"];
				this->resFood = (double)pRow["planet_res_food"];
				this->resPower = 0;
				this->wfMetal = (double)pRow["planet_wf_metal"];
				this->wfCrystal = (double)pRow["planet_wf_crystal"];
				this->wfPlastic = (double)pRow["planet_wf_plastic"];
				this->resPeople = (double)pRow["planet_people"];
			}
			else {
				this->userId = 0;
				this->planetUserMain = false;
				this->planetType = 0;
				this->resMetal = 0;
				this->resCrystal = 0;
				this->resPlastic = 0;
				this->resFuel = 0;
				this->resFood = 0;
				this->resPower = 0;
				this->wfMetal = 0;
				this->wfCrystal = 0;
				this->wfPlastic = 0;
				this->resPeople = 0;
			}
		}
		
		this->dataLoaded = true;
	}
	
	void Planet::saveData() {
		if (this->changedData) {
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();
		
			query << "UPDATE ";
			query << "	planets ";
			query << "SET ";
			query << "	planet_user_id='" << this->getUserId() << "', ";
			query << "	planet_name='" << this->codeName << "', ";
			query << "	planet_res_metal='" << this->getResMetal() << "', ";
			query << "	planet_res_crystal='" << this->getResCrystal() << "', ";
			query << "	planet_res_fuel='" << this->getResFuel() << "', ";
			query << "	planet_res_plastic='" << this->getResPlastic() << "', ";
			query << "	planet_res_food='" << this->getResFood() << "', ";
			query << "	planet_wf_metal='" << this->getWfMetal() << "', ";
			query << "	planet_wf_crystal='" << this->getWfCrystal() << "', ";
			query << "	planet_wf_plastic='" << this->getWfPlastic() << "', ";
			query << "	planet_people='" << this->getResPeople() << "' ";
			query << "WHERE ";
			query << "	id='" << this->getId() << "' ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
		}
		
		this->changedData = false;
	}
	
	double Planet::getWfMetal() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->wfMetal;
	}
	
	double Planet::getWfCrystal() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->wfCrystal;
	}
	
	double Planet::getWfPlastic() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->wfPlastic;
	}
	
	double Planet::getWfsum() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->getWfMetal() + this->getWfCrystal() + this->getWfPlastic();
	}
	
	double Planet::removeWfMetal(double metal) {
		if (!this->dataLoaded)
			this->loadData();
			
		this->changedData = true;
		if (metal<=this->wfMetal) {
			this->wfMetal -= metal;
			return metal;
		}
		else {
			metal = this->wfMetal;
			this->wfMetal = 0;
			return metal;
		}
	}
	
	double Planet::removeWfCrystal(double crystal) {
		if (!this->dataLoaded)
			this->loadData();
			
		this->changedData = true;
		if (crystal<=this->wfCrystal) {
			this->wfCrystal -= crystal;
			return crystal;
		}
		else {
			crystal = this->wfCrystal;
			this->wfCrystal = 0;
			return crystal;
		}
	}
	
	double Planet::removeWfPlastic(double plastic) {
		if (!this->dataLoaded)
			this->loadData();
			
		this->changedData = true;
		if (plastic<=this->wfPlastic) {
			this->wfPlastic -= plastic;
			return plastic;
		}
		else {
			plastic = this->wfPlastic;
			this->wfPlastic = 0;
			return plastic;
		}
	}
	
	double Planet::getResPeople() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->resPeople;
	}
	
	double Planet::removeResPeople(double people) {
		if (!this->dataLoaded)
			this->loadData();
			
		this->changedData = true;
		if (people<=this->resPeople) {
			this->resPeople -= people;
			return people;
		}
		else {
			people = this->resPeople;
			this->resPeople = 0;
			return people;
		}
	}
	
	bool Planet::getPlanetUserMain() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->planetUserMain;
	}
	
	short Planet::getPlanetType() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->planetType;
	}
