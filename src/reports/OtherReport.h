
#ifndef __OTHERREPORT__
#define __OTHERREPORT__

#include <string>
#include <vector>
#include <iostream>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Report.h"

/**
* OtherReport class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/


class OtherReport	: public Report
{
public:
	OtherReport(int userId=0, int entity1Id=0, int entity2Id=0, int timestamp=0, int fleetId=0, std::string action="" ) : Report() {
		this->type = "other";
		this->status = 0;
		this->ships = "";
		
		this->res0 = 0;
		this->res1 = 0;
		this->res2 = 0;
		this->res3 = 0;
		this->res4 = 0;
		this->res5 = 0;
		
		this->endtime=0;
		
		this->action = action;
		this->fleetId = fleetId;
		this->timestamp = timestamp;
		this->entity1Id = entity1Id;
		this->entity2Id = entity2Id;
		this->addUser(userId);
	}

	OtherReport(OtherReport* report) {	}
		
	OtherReport() {
		while (!this->users.empty()) {
			this->id = this->save(this->users.back());
			this->saveOtherReport();
			this->users.pop_back();
		}
		
	}
	
	void setStatus(unsigned short status);
	void setAction(std::string action);
	void setShips(std::string ships);
	void setRes(double res0=0,
				double res1=0,
				double res2=0,
				double res3=0,
				double res4=0,
				double res5=0);
	void setFleetId(unsigned int fleetId);
	void setEndtime(unsigned int endtime);
	
	void saveOtherReport();
	
private:
	std::string action;
	unsigned short status;
	
	std::string ships;
	
	double res0, res1, res2, res3, res4, res5;
	unsigned int fleetId;
	unsigned int endtime;
};

#endif
