
#include "Message.h"
#include "../util/Functions.h"
#include "../util/Log.h"

	std::string Message::getText() {
		return this->text;
	}
	
	std::string Message::getSubject() {
		return this->subject;
	}
	
	int Message::getType() {
		return this->type;
	}
	
	int Message::getEntityId() {
		return this->entityId;
	}
	
	int Message::getFleetId() {
		return this->fleetId;
	}

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
	
	void Message::addText(std::string text, short linebreaks) {
		this->text += text;
		while (linebreaks > 0) {
			this->text += "\n";
			linebreaks--;
		}
	}
	
	void Message::addSignature(std::string signature) {
		this->signature = signature;
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
				if ((*it)) {
					try	{
						query << "INSERT INTO "
							<< "	messages "
							<< "("
							<< "	message_user_from, "
							<< "	message_user_to, "
							<< "	message_timestamp, "
							<< "	message_cat_id "
							<< ") "
							<< "VALUES "
							<< "('0', '"
							<< (*it) << "', '"
							<< time(0) << "', '"
							<< this->type << "' "
							<< ");";
						std::cout << query.str() << std::endl;
						query.store();
						query.reset();
						
						query << "INSERT INTO "
							<< "	message_data "
							<< "("
							<< "	id, "
							<< "	subject, "
							<< "	text, "
							<< "	entity_id, "
							<< "	fleet_id "
							<< ") "
							<< "VALUES "
							<< "('" << query.insert_id() << "', "
							<< "'" << this->subject << "', ";
						
						std::string buff = this->text;
						// TODO: Dirty!! Hack. fix it
						if (buff.find("'") && !buff.find("\\'"))
							buff = etoa::addslashes(buff);
							
						query << "'" << buff << "', "
							<< "'" << this->entityId << "', "
							<< "'" << this->fleetId << "' "
							<< ");";
						std::cout << query.str() << std::endl;
						query.store();
						query.reset();
					}
					catch (mysqlpp::Exception* e)
					{
						LOG(LOG_ERR, "MySQL error while sending message: " << e->what()<<", Query: "<<query.str());
					}
				}
			}
		}
	}


