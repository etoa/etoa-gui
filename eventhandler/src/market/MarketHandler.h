
#ifndef __MARKETHANDLER__
#define __MARKETHANDLER__

#include <iostream>
#include <vector>
#include <ctime>
#include <cmath>
#include <string.h>

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../EventHandler.h"
#include "../objects/User.h"
#include "../util/Functions.h"
#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"
#include "../reports/MarketReport.h"

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
		~MarketHandler() {};
		void update();
		void MarketAuctionUpdate();
		static void update_config(std::vector<int> buy_res, std::vector<int> sell_res);
		static void addTradePoints(std::string userId,int points,bool sell,std::string reason);
	private:
		bool changes_;
		std::vector<int> changedPlanets_;		
	};
}
#endif
