
#ifndef __PLANETMANAGER__
#define __PLANETMANAGER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include <iostream>
#include <vector>
#include <algorithm>

#include "../MysqlHandler.h"
#include "../planet/PlanetEntity.h"

#define PLANETMANAGER_UPDATE_INTERVAL 300

namespace planet
{
	class PlanetManager
	{
	public:
		PlanetManager();
		~PlanetManager();
		
    void markForUpdate(int planetId);
    void markForUpdate(std::vector<int>* planetIds);
		void updatePlanet(int planetId);
		void updatePlanets();
		void updatePlanets(std::vector<int>* planetIds);
		std::vector<int>* getUpdateableUserPlanets();
	private:
    std::vector<int> planetsMarkedForUpdate;
	};
}

#endif
