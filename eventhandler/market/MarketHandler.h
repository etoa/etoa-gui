
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
		MarketHandler(mysqlpp::Connection* con)  : EventHandler(con) { this->changes_ = false; };
		void update();
		void MarketAuctionUpdate();
		static void update_config(mysqlpp::Connection* con_,std::vector<int> buy_res, std::vector<int> sell_res);
	private:
		bool changes_;
		std::vector<int> changedPlanets_;		
	};
}
#endif
