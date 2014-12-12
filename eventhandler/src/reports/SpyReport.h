
#ifndef __SPYREPORT__
#define __SPYREPORT__

#include <string>
#include <vector>
#include <iostream>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Report.h"

/**
* SpyReport class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/


class SpyReport	: public Report
{
public:
	SpyReport(int userId=0, int entity1Id=0, int entity2Id=0, int timestamp=0, int fleetId=0, int opponent1Id=0 ) : Report() {
		this->type = "spy";
		this->buildings = "";
		this->technologies = "";
		this->ships = "";
		this->defense = "";
		
		this->res0 = 0;
		this->res1 = 0;
		this->res2 = 0;
		this->res3 = 0;
		this->res4 = 0;
		this->res5 = 0;
		
		this->spydefense = 0;
		this->coverage = 0;
		
		this->opponent1Id = opponent1Id;
		this->fleetId = fleetId;
		this->timestamp = timestamp;
		this->entity1Id = entity1Id;
		this->entity2Id = entity2Id;
		this->addUser(userId);
	}

	SpyReport(SpyReport* report) {	}
		
	~SpyReport() {
		while (!this->users.empty()) {
			this->id = this->save(this->users.back());
			this->saveSpyReport();
			this->users.pop_back();
		}	
	}
	
	void setBuildings(std::string buildings);
	void setTechnologies(std::string technologies);
	void setShips(std::string ships);
	void setDefense(std::string defense);
	
	void setRes(double res0=0,
				double res1=0,
				double res2=0,
				double res3=0,
				double res4=0,
				double res5=0);
	
	void setFleetId(unsigned int fleetId);
	void setSpydefense(unsigned short spydefense);
	void setCoverage(unsigned short coverage);
	
	void saveSpyReport();
	
private:
	std::string buildings;
	std::string technologies;
	std::string ships;
	std::string defense;
	
	double res0, res1, res2, res3, res4, res5;
	unsigned int fleetId;
	unsigned short spydefense, coverage;
};

#endif
