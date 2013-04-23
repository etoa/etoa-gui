
#ifndef __PLANETMANAGER__
#define __PLANETMANAGER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include <vector>
#include <iostream>

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
		
		void updatePlanet(int planetId);
		void updatePlanets(std::vector<int>* planetIds);
		std::vector<int> getUpdateableUserPlanets();
	private:

	};
}

#endif
