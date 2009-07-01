
#ifndef __USER__
#define __USER__

#include <map>
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
			
			this->techAtWork = "";
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
		
		bool getPropertiesReturnMsg();
		
		double getTechBonus(std::string tech);
		unsigned int getTechLevel(std::string tech);
		
		std::string stealTech(User* victim);
		
		int getUserMain();
		int getPlanetsCount();
		
		std::string getTechString();
		
	private:
		int userId;
		int allianceId;
		std::string userNick;
		double points;
		
		SpecialistData* specialist;
		
		std::map<std::string, int> techs;
		std::string techAtWork;
		bool dataLoaded, techsLoaded;
		void loadData();
		void loadTechs();
};

#endif
