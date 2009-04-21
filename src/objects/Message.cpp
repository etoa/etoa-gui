
#include "Message.h"
#include "../util/Functions.h"

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
				if ((*it)) 
				{
					try	{
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
					std::cout << query.str() << std::endl;
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
					
					std::string buff = this->text;
					// TODO: Dirty!! Hack. fix it
					if (buff.find("'") && !buff.find("\\'"))
						buff = etoa::addslashes(buff);
							
					query << "'" << buff << "', ";
					query << "'" << this->entityId << "', ";
					query << "'" << this->fleetId << "' ";
					query << ");";
					std::cout << query.str() << std::endl;
					query.store();
					query.reset();
					}
					catch (mysqlpp::Exception* e)
					{
						std::cout << e->what() << std::endl;
						std::cout << query.str() << std::endl;
					}
				}
			}
		}
	}


