
#ifndef __USER__
#define __USER__

#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"

/**
* User class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class User	
{
	public:
		User(int userId) {
			this->userId = userId;
			
			this->dataLoaded = false;
		}
		
		~User() { }
		
		int getAllianceId();
		std::string getUserNick();
		
		void addCollectedWf(double res);
		void addCollectedAsteroid(double res);
		void addCollectedNebula(double res);
		
		bool getPropertiesReturnMsg();
		
	private:
		int userId;
		int allianceId;
		std::string userNick;
		
		bool dataLoaded;
		void loadData();
};

#endif
