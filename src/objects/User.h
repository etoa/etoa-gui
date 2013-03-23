
#ifndef __USER__
#define __USER__

#include <map>
#include <time.h>
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
		int getElorating();
		void addElorating(int newRating);
		
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
		unsigned int getTechLevel(std::string tech);
		
		std::string stealTech(User* victim);
		
		int getUserMain();
		int getPlanetsCount();
		bool isInactiv();
		
		std::string getTechString();
		
		inline int getUserId() const
		{
		    return this->userId;
		}
		
	private:
		int userId;
		int allianceId;
		std::string userNick;
		double points;
		int elorating;
		unsigned int spyattackCount;
		bool inactiv;
		
		SpecialistData* specialist;
		
		std::map<int, int> techs;
		unsigned int techAtWork;
		bool dataLoaded, techsLoaded;
		void loadData();
		void loadTechs();
};

#endif
