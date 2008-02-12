
#ifndef __PLANETMANAGER__
#define __PLANETMANAGER__

#include <vector>
#include <iostream>

namespace planet
{
	class PlanetManager
	{
	public:
		PlanetManager(mysqlpp::Connection* con) { this->con_ = con; };
		PlanetManager(mysqlpp::Connection* con, std::vector<int>* planetIds);	

		void updateValues(std::vector<int>* planetIds);
		void updateEconomy();
		void updateFields(int planetId, int& fieldsUsed, int& fieldsExtra);
		void PlanetManager::updateStorage(int planetId, std::vector<int>& store);
		void PlanetManager::updateProductionRates(int planetId, std::vector<double>& cnt, mysqlpp::Row& row);
		void PlanetManager::save(int planetId, std::vector<int>& store, std::vector<double>& cnt, int fieldsUsed, int fieldsExtra);

	private:
		mysqlpp::Connection* con_;
		std::vector<int>* planetIds_;
	};
}

#endif
