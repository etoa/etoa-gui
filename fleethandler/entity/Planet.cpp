
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
				this->userMain = (bool)pRow["planet_user_main"];
				this->codeName = std::string(pRow["planet_name"]);
				this->typeId = (short)pRow["planet_type_id"];
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
		}
		
		this->initResMetal = this->resMetal;
		this->initResCrystal = this->resCrystal;
		this->initResPlastic = this->resPlastic;
		this->initResFuel = this->resFuel;
		this->initResFood = this->resFood;
		this->initResPower = this->resPower;
		
		this->initWfMetal = this->initWfMetal;
		this->initWfCrystal = this->wfCrystal;
		this->initWfPlastic = this->wfPlastic;
		
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
			query << "	planet_res_metal=planet_res_metal+'" << (this->getResMetal() - this->initResMetal) << "', ";
			query << "	planet_res_crystal=planet_res_crystal+'" << (this->getResCrystal() - this->initResCrystal) << "', ";
			query << "	planet_res_fuel=planet_res_fuel+'" << (this->getResFuel() - this->initResFuel) << "', ";
			query << "	planet_res_plastic=planet_res_plastic+'" << (this->getResPlastic() - this->initResPlastic) << "', ";
			query << "	planet_res_food=planet_res_food+'" << (this->getResFood() - this->initResFood) << "', ";
			query << "	planet_wf_metal=planet_wf_metal+'" << (this->getWfMetal() - this->initWfMetal) << "', ";
			query << "	planet_wf_crystal=planet_wf_crystal+'" << (this->getWfCrystal() - this->initWfCrystal) << "', ";
			query << "	planet_wf_plastic=planet_wf_plastic+'" << (this->getWfPlastic() - this->initWfPlastic) << "', ";
			query << "	planet_people=planet_people+'" << (this->getResPeople() - this->initResPeople) << "' ";
			query << "WHERE ";
			query << "	id='" << this->getId() << "' ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
		}
		
		this->changedData = false;
	}
