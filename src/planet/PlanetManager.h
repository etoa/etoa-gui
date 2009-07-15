
#ifndef __PLANETMANAGER__
#define __PLANETMANAGER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include <vector>
#include <iostream>

#include "../MysqlHandler.h"
#include "../planet/PlanetEntity.h"

namespace planet
{
	class PlanetManager
	{
	public:
		PlanetManager();
		PlanetManager(std::vector<int>* planetIds);	

		void updateUserPlanets();
	private:
		PlanetEntity* planet_;
	};
}

#endif
