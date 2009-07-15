
#ifndef __SHIPHANDLER__
#define __SHIPHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include <vector>
#include <math.h>
#include <ctime>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

#include "ShipList.h"

/**
* Handles ship building updates
* 
* \author Nicolas Perrenoud <mrcage@etoa.ch>
*/
namespace ship
{
	class ShipHandler	: EventHandler
	{
	public:
		ShipHandler()  : EventHandler() { this->changes_ = false; }
		~ShipHandler() {}
		void update();
		inline bool changes() { return this->changes_; }
		inline std::vector<int> getChangedPlanets() { return this->changedPlanets_; }
	private:
		bool changes_;
		bool updatePlanet;
		std::vector<int> changedPlanets_;		
	};
}
#endif
