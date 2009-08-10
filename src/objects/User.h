
#ifndef __USER__
#define __USER__

#include <map>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"
#include "../util/Functions.h"
#include "../data/DataHandler.h"
#include "../data/Data.h"
#include "../data/SpecialistData.h"

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
			
			this->specialist= NULL;
			
			this->techAtWork = 0;
		}
		
		~User() { }
		
		int getAllianceId();
		std::string getUserNick();
		double getUserPoints();
		
		SpecialistData* getSpecialist();
		
		void setDiscovered(short absX, short absY);
		void setLastInvasion();
		
		void addCollectedWf(double res);
		void addCollectedAsteroid(double res);
		void addCollectedNebula(double res);
		void addRaidedRes(double res);
		
		void addSpyattackCount();
		int getSpyattackCount();
		
		bool getPropertiesReturnMsg();
		
		double getTechBonus(unsigned int tech);
		unsigned int getTechLevel(unsigned int tech);
		
		std::string stealTech(User* victim);
		
		int getUserMain();
		int getPlanetsCount();
		
		std::string getTechString();
		
	private:
		int userId;
		int allianceId;
		std::string userNick;
		double points;
		unsigned int spyattackCount;
		
		SpecialistData* specialist;
		
		std::map<int, int> techs;
		unsigned int techAtWork;
		bool dataLoaded, techsLoaded;
		void loadData();
		void loadTechs();
};

#endif
