
#ifndef __MARKETHANDLER__
#define __MARKETHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"

/**
* Handles market updates
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace market
{
	class MarketHandler	: EventHandler
	{
	public:
		MarketHandler(mysqlpp::Connection* con)  : EventHandler(con) { this->changes_ = false; }
		void update();
		static void MarketAuctionUpdate(mysqlpp::Connection* con)
		inline bool changes() { return this->changes_; }
	private:
		bool changes_;
		std::vector<int> changedPlanets_;		
	};
}
#endif
