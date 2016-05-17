
#include "Nebula.h"

	void Nebula::loadData() {
		
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	nebulas ";
		query << "WHERE ";
		query << "	id=" << this->getId() << " ";
		query << "LIMIT 1;";
		RESULT_TYPE nRes = query.store();
		query.reset();
		
		if (nRes) {
			int nSize = nRes.size();
			
			if (nSize>0) {
				mysqlpp::Row nRow = nRes.at(0);
				
				this->resMetal = (double)nRow["res_metal"];
				this->resCrystal = (double)nRow["res_crystal"];
				this->resPlastic = (double)nRow["res_plastic"];
				this->resFuel = (double)nRow["res_fuel"];
				this->resFood = (double)nRow["res_food"];
				this->resPower = (double)nRow["res_power"];
			}
		}
		
		this->initResMetal = this->resMetal;
		this->initResCrystal = this->resCrystal;
		this->initResPlastic = this->resPlastic;
		this->initResFuel = this->resFuel;
		this->initResFood = this->resFood;
		this->initResPower = this->resPower;
		
		this->initWfMetal = this->resMetal;
		this->initWfCrystal = this->wfCrystal;
		this->initWfPlastic = this->wfPlastic;
		
		this->entityUser = new User(this->userId);
		
		this->dataLoaded = true;
	}
	
	void Nebula::saveData() {
		
		Config &config = Config::instance();
		
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		
		// Check if there are still enough resources in the field, if not delete it and create a new one
		if (this->getResSum() < config.nget("nebula_ress",1)) {
			// Delete the old one and replace it with an empty field
			query << "UPDATE ";
			query << "	entities ";
			query << "SET ";
			query << "	code='e', ";
			query << " lastvisited=0 ";
			query << "WHERE ";
			query << "	id=" << this->getId() << " ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
			
			query << "DELETE FROM";
			query << "	nebulas ";
			query << "WHERE ";
			query << " id=" << this->getId() << " ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
			
			query << "INSERT INTO ";
			query << " space ";
			query << "(";
			query << "	id ";
			query << ") ";
			query << "VALUES ";
			query << "(";
			query << "" << this->getId() << ");";
			query.store();
			query.reset();
			
			// Create a new one
			double newRess = config.nget("nebula_ress",1) + (rand() % (int)(config.nget("nebula_ress",2) - config.nget("nebula_ress",1) + 1));
			
			// Check if there is an empty field left
			query << "SELECT ";
			query << "	id ";
			query << "FROM ";
			query << "	entities ";
			query << "WHERE ";
			query << "	code='e' ";
			query << "ORDER BY ";
			query << " RAND() ";
			query << "LIMIT 1;";
			RESULT_TYPE searchRes = query.store();
			query.reset();
			
			if (searchRes) {
				int searchSize = searchRes.size();
				
				// if there is, create it
				if (searchSize > 0) {
					mysqlpp::Row searchRow = searchRes.at(0);
					
					query << "UPDATE ";
					query << "	entities ";
					query << "SET ";
					query << "	code='n' ";
					query << "WHERE ";
					query << "	id=" << searchRow["id"] << " ";
					query << "LIMIT 1;";
					query.store();
					query.reset();
					
					query << "INSERT INTO ";
					query << "	nebulas ";
					query << "(";
					query << "	id, ";
					query << "	res_crystal ";
					query << ") ";
					query << "VALUES ";
					query << "(";
					query << "" << searchRow["id"] << ", ";
					query << "" << newRess << ");";
					query.store();
					query.reset();
					
					query << "DELETE FROM ";
					query << "	space ";
					query << "WHERE ";
					query << " id=" << searchRow["id"] << " ";
					query << "LIMIT 1;";
					query.store();
					query.reset();
				}
			}
		}
		else if (this->changedData) {
			// Update the nebula field with the new resources
			query << "UPDATE ";
			query << "	nebulas ";
			query << "SET ";
			query << "	res_metal=res_metal+" << (this->getResMetal() - this->initResMetal) << ", ";
			query << "	res_crystal=res_crystal+" << (this->getResCrystal() - this->initResCrystal) << ", ";
			query << "	res_plastic=res_plastic+" << (this->getResPlastic() - this->initResPlastic) << ", ";
			query << "	res_fuel=res_fuel+" << (this->getResFuel() - this->initResFuel) << ", ";
			query << "	res_food=res_food+" << (this->getResFood() - this->initResFood) << ", ";
			query << "	res_power=res_power+" << (this->getResPower() - this->initResPower) << " ";
			query << "WHERE ";
			query << "	id=" << this->getId() << " ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
		}
		
		this->changedData = false;
	}
