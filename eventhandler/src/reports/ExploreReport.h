
#ifndef __EXPLOREREPORT__
#define __EXPLOREREPORT__

#include <string>
#include <vector>
#include <iostream>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Report.h"

/**
* ExploreReport class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/


class ExploreReport	: public Report
{
public:
	ExploreReport(int userId=0, int entity1Id=0, int entity2Id=0, int timestamp=0 ) : Report() {
		this->subject = "Erkundung";
		this->type = "explore";

		this->timestamp=timestamp;
		this->entity1Id=entity1Id;
		this->entity2Id=entity2Id;
		this->addUser(userId);
	}

	ExploreReport(ExploreReport* report) {	}

	~ExploreReport() {
		std::cout << "one\n";
		while (!this->users.empty()) {
			std::cout << "one\n";
			this->save(this->users.back());
			this->users.pop_back();
		}

	}
};

#endif
