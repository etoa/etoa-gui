
#ifndef __MARKETREPORT__
#define __MARKETREPORT__

#include <string>
#include <vector>
#include <iostream>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Report.h"

/**
* MarketReport class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/


class MarketReport	: public Report
{
public:
	MarketReport(int userId=0,
				 int entity1Id=0,
				 int recordId=0,
				 int timestamp=0,
				 int opponent1Id=0,
				 int entity2Id=0,
				 int fleet1Id=0,
				 int fleet2Id=0) : Report() {
		this->type = "market";
		this->subtype = "other";

		this->buy0 = 0;
		this->buy1 = 0;
		this->buy2 = 0;
		this->buy3 = 0;
		this->buy4 = 0;
		this->buy5 = 0;

		this->sell0 = 0;
		this->sell1 = 0;
		this->sell2 = 0;
		this->sell3 = 0;
		this->sell4 = 0;
		this->sell5 = 0;

		this->factor = 0;

		this->recordId = recordId;
		this->timestamp = timestamp;
		this->fleet1Id = fleet1Id;
		this->fleet2Id = fleet2Id;
		this->entity1Id = entity1Id;
		this->entity2Id = entity2Id;
		this->opponent1Id = opponent1Id;
		this->addUser(userId);
	}

	MarketReport(MarketReport* report) {	}

	~MarketReport() {
		while (!this->users.empty()) {
			this->id = this->save(this->users.back());
			this->saveMarketReport();
			this->users.pop_back();
		}

	}

	void setBuy(unsigned int res0,
				unsigned int res1,
				unsigned int res2,
				unsigned int res3,
				unsigned int res4,
				unsigned int res5);

	void setSell(unsigned int res0,
				unsigned int res1,
				unsigned int res2,
				unsigned int res3,
				unsigned int res4,
				unsigned int res5);

	void setFleet1Id(unsigned int fleet1Id);
	void setFleet2Id(unsigned int fleet2Id);
	void setFactor(double factor);

	void saveMarketReport();

private:
	unsigned int buy0, buy1, buy2, buy3, buy4, buy5;
	unsigned int sell0, sell1, sell2, sell3, sell4, sell5;
	unsigned int fleet1Id, fleet2Id;
	unsigned int recordId;
	double factor;
};

#endif
