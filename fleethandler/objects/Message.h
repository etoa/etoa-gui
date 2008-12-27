
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
		}
		
		~Message() {
			this->send();
		}
		
		void addUserId(int userId);
		void addType(int type);
		void addFleetId(int fleetId);
		void addEntityId(int entityId);
		void addSubject(std::string subject);
		void addText(std::string text);
		void dontSend();
		
		
	private:
		std::vector<int> users;
		int type;
		int fleetId;
		int entityId;
		std::string subject;
		std::string text;
		
		bool toSend;
		
		void send();
};

#endif
