
#ifndef __LOG__
#define __LOG__

#include <string>
#include <vector>
#include <iostream>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"

/**
* Log class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Log	
{
	public:
		Log() {
			this->text = "";
			this->fleetResStart = "untouched";
			this->fleetResEnd = "untouched";
			this->fleetShipsStart = "untouched";
			this->fleetShipsEnd = "untouched";
			this->entityResStart = "untouched";
			this->entityResEnd = "untouched";
			this->entityShipsStart = "untouched";
			this->entityShipsEnd = "untouched";
		}
		
		~Log() {
			this->save();
		}
		
		void addFleetId(int fleetId);		
		void addFleetUserId(int userId);
		void addEntityUserId(int userId);
		void addEntityToId(int entityId);
		void addEntityFromId(int entityId);
		void addLaunchtime(int launchtime);
		void addLandtime(int landtime);
		void addAction(std::string action);
		void addStatus(short status);
		void addText(std::string text);
		void addFleetResStart(std::string res);
		void addFleetResEnd(std::string res);
		void addFleetShipsStart(std::string ships);
		void addFleetShipsEnd(std::string ships);
		void addEntityResStart(std::string res);
		void addEntityResEnd(std::string res);
		void addEntityShipsStart(std::string ships);
		void addEntityShipsEnd(std::string ships);
		
	private:
		int fleetId;
		int fleetUserId;
		int entityUserId;
		int entityToId, entityFromId;
		int landtime, launchtime;
		std::string action;
		short status;
		std::string text;
		std::string fleetResStart, fleetResEnd;
		std::string fleetShipsStart, fleetShipsEnd;
		std::string entityResStart, entityResEnd;
		std::string entityShipsStart, entityShipsEnd;
		
		void save();
};

#endif
