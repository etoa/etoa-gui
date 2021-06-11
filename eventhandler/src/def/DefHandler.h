
#ifndef __DEFHANDLER__
#define __DEFHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include <vector>
#include <math.h>
#include <ctime>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

#include "DefList.h"

/**
* Handles defense updates
*
* \author Nicolas Perrenoud <mrcage@etoa.ch>
*/
namespace def
{
	class DefHandler	: EventHandler
	{
	public:
		DefHandler()  : EventHandler() { this->changes_ = false; }
		~ DefHandler() {}
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
