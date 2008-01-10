
#ifndef __SHIPHANDLER__
#define __SHIPHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"

namespace ship
{
	class ShipHandler	: EventHandler
	{
	public:
		ShipHandler(mysqlpp::Connection* con)  : EventHandler(con) { this->changes_ = false; }
		void update();
		inline bool changes() { return this->changes_; }
		inline std::vector<int> getChangedPlanets() { return this->changedPlanets_; }
	private:
		bool changes_;
		std::vector<int> changedPlanets_;		
	};
}
#endif
