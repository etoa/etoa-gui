
#ifndef __TECHHANDLER__
#define __TECHHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include <time.h>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

/**
* Handles technology research updates
* 
* \author Nicolas Perrenoud <mrcage@etoa.ch>
*/
namespace tech
{
	class TechHandler	: EventHandler
	{
	public:
		TechHandler()  : EventHandler() { this->changes_ = false; }
		~TechHandler() {};
		void update();
		inline bool changes() { return this->changes_; }
		inline std::vector<int> getChangedUsers() { return this->changedUsers_; }
	private:
		bool changes_;
		std::vector<int> changedUsers_;	
	};
}
#endif
