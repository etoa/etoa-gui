
#ifndef __BACKHANDLER__
#define __BACKHANDLER__

#include <mysql++/mysql++.h>

#include "../FleetHandler.h"

namespace back
{
	class BackHandler : FleetHandler
	{
	public:
		BackHandler(mysqlpp::Connection* con)  : FleetHandler(con) { this->changes_ = false; }
		void update(mysqlpp::Row);
		inline bool changes() { return this->changes_; }
		inline std::vector<int> getChangedPlanets() { return this->changedPlanets_; }
	private:
		bool changes_;
		std::vector<int> changedPlanets_;
	};
}
#endif