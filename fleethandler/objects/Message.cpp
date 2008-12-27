
#include "Message.h"

	void Message::addUserId(int userId) {
		this->users.push_back(userId);
	}
	
	void Message::addType(int type) {
		this->type = type;
	}
	
	void Message::addFleetId(int fleetId) {
		this->fleetId = fleetId;
	}
	
	void Message::addEntityId(int entityId) {
		this->entityId = entityId;
	}
	
	void Message::addSubject(std::string subject) {
		this->subject = subject;
	}
	
	void Message::addText(std::string text) {
		this->text += text + "\n";
	}
	
	void Message::dontSend() {
		this->toSend = false;
	}
	
	void Message::send() {
		if (this->toSend) {
			My &my = My::instance();
			mysqlpp::Connection *con_ = my.get();
			
			mysqlpp::Query query = con_->query();
			
			std::vector<int>::iterator it;
			for ( it=this->users.begin() ; it < this->users.end(); it++ ) {
				query << "INSERT INTO ";
				query << "	messages ";
				query << "(";
				query << "	message_user_from, ";
				query << "	message_user_to, ";
				query << "	message_timestamp, ";
				query << "	message_cat_id ";
				query << ") ";
				query << "VALUES ";
				query << "('0', '";
				query << (*it) << "', '";
				query << time(0) << "', '";
				query << this->type << "' ";
				query << ");";
				query.store();
				query.reset();
			
				query << "INSERT INTO ";
				query << "	message_data ";
				query << "(";
				query << "	id, ";
				query << "	subject, ";
				query << "	text, ";
				query << "	entity_id, ";
				query << "	fleet_id ";
				query << ") ";
				query << "VALUES ";
				query << "('" << con_->insert_id() << "', ";
				query << "'" << this->subject << "', ";
				query << "'" << this->text << "', ";
				query << "'" << this->entityId << "', ";
				query << "'" << this->fleetId << "', ";
				query << ");";
				query.store();
				query.reset();
			}
		}
	}


