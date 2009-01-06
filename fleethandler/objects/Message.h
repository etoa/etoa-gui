
#ifndef __MESSAGE__
#define __MESSAGE__

#include <string>
#include <vector>
#include <iostream>
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"

/**
* Message class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Message	
{
	public:
		Message() {	
			this->toSend = true;
			this->text = "";
			this->subject = "";
			this->type = 0;
		}
		
		Message(Message* message) {	
			this->toSend = true;
			this->text = message->getText();
			this->type = message->getType();
			this->subject = message->getSubject();
			this->entityId = message->getEntityId();
			this->fleetId = message->getFleetId();
			this->users.clear();;
		}
		
		~Message() {
			this->send();
		}
		
		std::string getText();
		std::string getSubject();
		int getType();
		int getEntityId();
		int getFleetId();
		
		void addUserId(int userId);
		void addType(int type);
		void addFleetId(int fleetId);
		void addEntityId(int entityId);
		void addSubject(std::string subject);
		void addText(std::string text, short linebreaks=0);
		void addSignature(std::string signature);
		void dontSend();
		
		
	private:
		std::vector<int> users;
		int type;
		int fleetId;
		int entityId;
		std::string subject;
		std::string text;
		std::string signature;
		
		bool toSend;
		
		void send();
};

#endif
