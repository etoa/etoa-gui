
#ifndef __BUILDINGHANDLER__
#define __BUILDINGHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include <ctime>
#include <vector>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

/**
* Handles building updates
* 
* \author Nicolas Perrenoud <mrcage@etoa.ch>
*/
namespace building
{
	class BuildingHandler	: EventHandler
	{
	public:
		BuildingHandler()  : EventHandler() { this->changes_ = false; }
		~BuildingHandler() {}
		void update();
		inline bool changes() { return this->changes_; }
		inline std::vector<int> getChangedPlanets() { return this->changedPlanets_; }
	private:
		bool changes_;
		std::vector<int> changedPlanets_;		
	};
}
#endif
