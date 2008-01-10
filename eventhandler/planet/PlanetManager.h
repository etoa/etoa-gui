
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

		void updateValues();
		void updateEconomy();
			
	private:
		mysqlpp::Connection* con_;
		std::vector<int>* planetIds_;
	};
}

#endif
