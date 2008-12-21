
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
		query << "	id='" << this->getId() << "' ";
		query << "LIMIT 1;";
		mysqlpp::Result nRes = query.store();
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
			else {
				this->resMetal = 0;
				this->resCrystal = 0;
				this->resPlastic = 0;
				this->resFuel = 0;
				this->resFood = 0;
				this->resPower = 0;
			}
		}
		
		this->dataLoaded = true;
	}
	
	void Nebula::saveData() {
	
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		
		// Check if there are still enough resources in the field, if not delete it and create a new one
		if (this->getResSum() < config.nget("nebula_action",2)) {
			// Delete the old one and replace it with an empty field
			query << "UPDATE ";
			query << "	entities ";
			query << "SET ";
			query << "	code='e', ";
			query << " lastvisited='0' ";
			query << "WHERE ";
			query << "	id='" << this->getId() << "' ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
			
			query << "DELETE FROM";
			query << "	nebulas ";
			query << "WHERE ";
			query << " id='" << this->getId() << "' ";
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
			query << "'" << this->getId() << "');";
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
			mysqlpp::Result searchRes = query.store();
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
					query << "	id='" << searchRow["id"] << "' ";
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
					query << "'" << searchRow["id"] << "', ";
					query << "'" << newRess << "');";
					query.store();
					query.reset();
					
					query << "DELETE FROM ";
					query << "	space ";
					query << "WHERE ";
					query << " id='" << searchRow["id"] << "' ";
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
			query << "	res_metal='" << this->getResMetal() << "', ";
			query << "	res_crystal='" << this->getResCrystal() << "', ";
			query << "	res_plastic='" << this->getResPlastic() << "', ";
			query << "	res_fuel='" << this->getResFuel() << "', ";
			query << "	res_food='" << this->getResFood() << "', ";
			query << "	res_power='" << this->getResPower() << "' ";
			query << "WHERE ";
			query << "	id='" << this->getId() << "' ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
		}
		
		this->changedData = false;
	}
