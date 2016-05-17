
#include "Planet.h"
	
	void Planet::loadData() {
		Config &config = Config::instance();
		
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		query << "SELECT "
			<< "	planet_user_id, "
			<< "	planet_user_main, "
			<< "	planet_name, "
			<< "	planet_type_id, "
			<< "	planet_res_metal, "
			<< "	planet_res_crystal, "
			<< "	planet_res_fuel, "
			<< "	planet_res_plastic, "
			<< "	planet_res_food, "
			<< "	planet_bunker_metal, "
			<< "	planet_bunker_crystal, "
			<< "	planet_bunker_plastic, "
			<< "	planet_bunker_fuel, "
			<< "	planet_bunker_food, "
			<< "	planet_wf_metal, "
			<< "	planet_wf_crystal, "
			<< "	planet_wf_plastic, "
			<< "	planet_people, "
			<< "	planet_fields, "
			<< "	planet_last_updated, "
			<< "	planet_user_changed, "
			<< "	planet_last_user_id "
			<< "FROM "
			<< "	planets "
			<< "WHERE "
			<< "	id='" << this->getId() << "' "
			<< "LIMIT 1;";
		RESULT_TYPE pRes = query.store();
		query.reset();
		
		if (pRes) {
			int pSize = pRes.size();
			
			if (pSize>0) {
				mysqlpp::Row pRow = pRes.at(0);
				this->userId = (int)pRow["planet_user_id"];
				this->userMain = (bool)pRow["planet_user_main"];
				this->codeName = std::string(pRow["planet_name"]);
				this->typeId = (short)pRow["planet_type_id"];
				this->resMetal = (double)pRow["planet_res_metal"];
				this->resCrystal = (double)pRow["planet_res_crystal"];
				this->resPlastic = (double)pRow["planet_res_plastic"];
				this->resFuel = (double)pRow["planet_res_fuel"];
				this->resFood = (double)pRow["planet_res_food"];
				this->resPower = 0;
				this->bunkerMetal = (unsigned int)pRow["planet_bunker_metal"];
				this->bunkerCrystal = (unsigned int)pRow["planet_bunker_crystal"];
				this->bunkerPlastic = (unsigned int)pRow["planet_bunker_plastic"];
				this->bunkerFuel = (unsigned int)pRow["planet_bunker_fuel"];
				this->bunkerFood = (unsigned int)pRow["planet_bunker_food"];
				this->wfMetal = (double)pRow["planet_wf_metal"];
				this->wfCrystal = (double)pRow["planet_wf_crystal"];
				this->wfPlastic = (double)pRow["planet_wf_plastic"];
				this->resPeople = (double)pRow["planet_people"];
				
				this->fields = (int)pRow["planet_fields"];
				this->lastUpdated = (int)pRow["planet_last_updated"];
				this->userChanged = (int)pRow["planet_user_changed"];
				this->lastUserId = (int)pRow["planet_last_user_id"];
			}
		}
		
		this->initResMetal = this->resMetal;
		this->initResCrystal = this->resCrystal;
		this->initResPlastic = this->resPlastic;
		this->initResFuel = this->resFuel;
		this->initResFood = this->resFood;
		this->initResPeople = this->resPeople;
		this->initResPower = this->resPower;
		
		this->initWfMetal = this->wfMetal;
		this->initWfCrystal = this->wfCrystal;
		this->initWfPlastic = this->wfPlastic;
		
		this->entityUser = new User(this->userId);
		
		if (this->typeId == config.nget("gasplanet", 0)) {
			this->codeName = "Gasplanet";
			this->updateGasPlanet();
		}
		
		this->dataLoaded = true;
	}
	
	void Planet::updateGasPlanet() {
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		
		int ptime = time;
		//if the planet was not updated yet, set the actual time minus an hour
		if (this->lastUpdated == 0) this->lastUpdated = ptime - 3600;
		double tlast = ptime - this->lastUpdated;
		tlast = this->resFuel + tlast*(double)config.nget("gasplanet", 1)/3600.0;
					
		double pSize = (double)config.nget("gasplanet", 2)*this->fields;
		this->resFuel = std::min(tlast,pSize);
		
		this->lastUpdated = time;
		this->changedData = true;
	}
	
	void Planet::saveData() {
		if (this->getCount()!=this->getInitCount() || this->shipsSave) {
			while (!objects.empty()) {
				Object* object = objects.back();
				delete object;
				objects.pop_back();
			}
			
			while (!def.empty()) {
				Object* object = def.back();
				delete object;
				def.pop_back();
			}
			while (!fleets.empty()) {
				Fleet* fleet = fleets.back();
				delete fleet;
				fleets.pop_back();
			}
		}
		
		if (this->changedData) {
			Config &config = Config::instance();
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();
			
			if (this->typeId == config.nget("gasplanet", 0))
				this->codeName = "";
			
			query << "UPDATE ";
			query << "	planets ";
			query << "SET ";
			query << "	planet_user_id='" << this->getUserId() << "', ";
			query << "	planet_res_metal=planet_res_metal+'" << (this->getResMetal() - this->initResMetal) << "', ";
			query << "	planet_res_crystal=planet_res_crystal+'" << (this->getResCrystal() - this->initResCrystal) << "', ";
			query << "	planet_res_fuel=planet_res_fuel+'" << (this->getResFuel() - this->initResFuel) << "', ";
			query << "	planet_res_plastic=planet_res_plastic+'" << (this->getResPlastic() - this->initResPlastic) << "', ";
			query << "	planet_res_food=planet_res_food+'" << (this->getResFood() - this->initResFood) << "', ";
			query << "	planet_wf_metal=planet_wf_metal+'" << (this->getWfMetal() - this->initWfMetal) << "', ";
			query << "	planet_wf_crystal=planet_wf_crystal+'" << (this->getWfCrystal() - this->initWfCrystal) << "', ";
			query << "	planet_wf_plastic=planet_wf_plastic+'" << (this->getWfPlastic() - this->initWfPlastic) << "', ";
			query << "	planet_people=planet_people+'" << (this->getResPeople() - this->initResPeople) << "', ";
			if (this->userChanged) {
				query << " planet_user_changed='" << this->userChanged << "', ";
				query << " planet_last_user_id='" << this->lastUserId << "', ";
				query << "	planet_name=" << mysqlpp::quote << this->codeName << ", ";
			}
			query << "	planet_last_updated='" << this->lastUpdated << "' ";
			query << "WHERE ";
			query << "	id='" << this->getId() << "' ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
		}
		
		this->changedData = false;
	}
