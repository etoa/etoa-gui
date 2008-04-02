
#ifndef __MARKETHANDLER__
#define __MARKETHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

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
		MarketHandler()  : EventHandler() { this->changes_ = false; };
		void update();
		void MarketAuctionUpdate();
		static void update_config(std::vector<int> buy_res, std::vector<int> sell_res);
	private:
		bool changes_;
		std::vector<int> changedPlanets_;		
	};
}
#endif
