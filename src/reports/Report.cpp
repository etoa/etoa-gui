#include "../util/Log.h"
#include "Report.h"

int Report::getId() {
	try {
		if (this->id>0) return this->id;
		else throw -1;
	}
	catch (int e) {
		std::cout << "Error: Report wasn't saved yet or failed saving!" << std::endl;
	}
	return -1;
}

void Report::setType(std::string type) {
	this->type = type;
}

void Report::setSubtype(std::string subtype) {
	this->subtype = subtype;
}

void Report::setTimestamp(unsigned int timestamp) {
	this->timestamp = (timestamp) ? timestamp : std::time(NULL);
}

void Report::setSubject(std::string subject) {
	this->subject = subject;
}

void Report::setContent(std::string content) {
	this->content = content;
}

void Report::addUser(int userId) {
	if (userId>0) {
		std::vector<int>::iterator it;
		for (it = this->users.begin(); it < this->users.end(); it++ )
			if (*it == userId) return;
		this->users.push_back(userId);	
	}
}

void Report::setAllianceId(unsigned int allianceId) {
	this->allianceId = allianceId;
}

void Report::setEntity1Id(unsigned int entity1Id) {
	this->entity1Id = entity1Id;
}

void Report::setEntity2Id(unsigned int entity2Id) {
	this->entity2Id = entity2Id;
}

void Report::setOpponent1Id(unsigned int opponent1Id) {
	this->opponent1Id = opponent1Id;
}

int Report::save(int userId) {
	My &my = My::instance();
	mysqlpp::Connection *con_ = my.get();
	
	mysqlpp::Query query = con_->query();
	
	try	{
		if (!this->timestamp) this->timestamp = std::time(NULL);
		
		query << "INSERT INTO "
			<< "	`reports` "
			<< "( "
			<< "	`timestamp`, "
			<< "	`type`, "
			<< "	`user_id`, "
			<< "	`alliance_id`, "
			<< "	`subject`, "
			<< "	`content`, "
			<< "	`entity1_id`, "
			<< "	`entity2_id`, "
			<< "	`opponent1_id`"
			<< ") "
			<< "VALUES "
			<< "( "
			<< "	'" << this->timestamp << "', "
			<< "	'" << this->type << "', "
			<< "	'" << userId << "', "
			<< "	'" << this->allianceId << "', "
			<< "	'" << this->subject << "', "
			<< "	'" << this->content << "', "
			<< "	'" << this->entity1Id << "', "
			<< "	'" << this->entity2Id << "', "
			<< "	'" << this->opponent1Id << "' "
			<< ");";
		query.store();
		query.reset();

		return query.insert_id();
	}
		catch (mysqlpp::Exception* e)
		{
			LOG(LOG_ERR, "MySQL error while saving report: " << e->what()<<", Query: "<<query.str());
		}
	return 0;
}


