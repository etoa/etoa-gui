
#ifndef __DEFHANDLER__
#define __DEFHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"

namespace def
{
	class DefHandler	: EventHandler
	{
	public:
		DefHandler(mysqlpp::Connection* con)  : EventHandler(con) { this->changes_ = false; }
		void update();
		inline bool changes() { return this->changes_; }
		inline std::vector<int> getChangedPlanets() { return this->changedPlanets_; }
	private:
		bool changes_;
		std::vector<int> changedPlanets_;		
	};
}
#endif
