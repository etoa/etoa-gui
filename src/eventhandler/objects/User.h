
#ifndef __USER__
#define __USER__

#include <map>
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"
#include "../functions/Functions.h"
#include "../data/DataHandler.h"

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
			this->techsLoaded = false;
			
			this->techAtWork = "";
		}
		
		~User() { }
		
		int getAllianceId();
		std::string getUserNick();
		double getUserPoints();
		
		void setDiscovered(short absX, short absY);
		
		void addCollectedWf(double res);
		void addCollectedAsteroid(double res);
		void addCollectedNebula(double res);
		
		bool getPropertiesReturnMsg();
		
		double getTechBonus(std::string tech);
		double getTechLevel(std::string tech);
		
		std::string stealTech(User* victim);
		
		int getUserMain();
		int getPlanetsCount();
		
		std::string getTechString();
		
	private:
		int userId;
		int allianceId;
		std::string userNick;
		double points;
		
		std::map<std::string, int> techs;
		std::string techAtWork;
		bool dataLoaded, techsLoaded;
		void loadData();
		void loadTechs();
};

#endif
