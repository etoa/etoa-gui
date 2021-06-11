
#ifndef __REPORT__
#define __REPORT__

#include <string>
#include <ctime>
#include <vector>
#include <iostream>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"

/**
* Report class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/


class Report
{
public:
	Report() {
		this->content = "";
		this->subject = "";
		this->type = "other";
		this->subtype = "other";

		this->timestamp=0;
		this->allianceId=0;
		this->entity1Id=0;
		this->entity2Id=0;
		this->opponent1Id=0;
	}

	Report(Report* report) {
	}

	virtual ~Report() {
		while (!this->users.empty()) {
			this->save(this->users.back());
			this->users.pop_back();
		}
	}

	int getId();

	void setType(std::string type);
	void setSubtype(std::string subtype);
	void setTimestamp(unsigned int timestamp=0);
	void setSubject(std::string subject);
	void setContent(std::string content);
	void addUser(int userId);
	void setAllianceId(unsigned int allianceId);
	void setEntity1Id(unsigned int entity1Id);
	void setEntity2Id(unsigned int entity2Id);
	void setOpponent1Id(unsigned int opponent1Id);

protected:
	int save(int userId);

	std::string type;
	std::string subtype;
	unsigned int id;
	unsigned int timestamp;
	std::string subject;
	std::string content;
	std::vector<int> users;
	unsigned int allianceId;
	unsigned int entity1Id;
	unsigned int entity2Id;
	unsigned int opponent1Id;
};

#endif
