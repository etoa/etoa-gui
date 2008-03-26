
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
		void updateEconomy(int planetId, mysqlpp::Row& row, std::vector<double>& ressource);
		void updateFields(int planetId, int& fieldsUsed, int& fieldsExtra);
		void updateStorage(int planetId, std::vector<int>& store);
		void updateProductionRates(int planetId, std::vector<double>& cnt, mysqlpp::Row& row);
		void save(int planetId, std::vector<int>& store, std::vector<double>& cnt, std::vector<double>& ressource, int fieldsUsed, int fieldsExtra);
		void updateGasPlanets();
		void updateUserPlanets();
		void saveRes(int planetId, std::vector<double>& ressource);

	private:
		mysqlpp::Connection* con_;
		std::vector<int>* planetIds_;
	};
}

#endif
