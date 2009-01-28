
#include "User.h"

	int User::getAllianceId() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->allianceId;
	}
	
	std::string User::getUserNick() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->userNick;
	}
	
	double User::getUserPoints() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->points;
	}		
	
	void User::setDiscovered(short absX, short absY) {
		
		Config &config = Config::instance();
		int sxNum = config.nget("num_of_sectors",1);
		int cxNum = config.nget("num_of_cells",1);
		int syNum = config.nget("num_of_sectors",2);
		int cyNum = config.nget("num_of_cells",2);
		
		// the mask
		char mask[10000] = "";
		
		
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	discoverymask ";
		query << "FROM ";
		query << "	users ";
		query << "WHERE ";
		query << "	user_id='" << this->userId << "' ";
		query << "LIMIT 1;";
		mysqlpp::Result maskRes = query.store();
		query.reset();
		
		if (maskRes) {
			int maskSize = maskRes.size();
			
			if (maskSize > 0) {
				mysqlpp::Row maskRow = maskRes.at(0);
				strcpy( mask, maskRow["discoverymask"]);
			}
		}
						
		for (int x = absX - 1; x <= absX + 1; x++) {
			for (int y = absY - 1; y <= absY + 1; y++) {
				int pos = x + (cyNum * syNum) * (y - 1) - 1;
				if (pos >= 0 && pos <= sxNum * syNum * cxNum * cyNum) {
					mask[pos] = '1';
				}
			}
		}	
						
		// Update the mask
		query << "UPDATE ";
		query << "	users ";
		query << "SET ";
		query << " discoverymask='" << mask << "' ";
		query << "WHERE ";
		query << "	user_id='" << this->userId << "' ";
		query << "LIMIT 1;";
		query.store();
		query.reset();
	}
	
	bool User::getPropertiesReturnMsg() {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	fleet_rtn_msg ";
		query << "FROM ";
		query << "	user_properties ";
		query << "WHERE ";
		query << "	id=" << this->userId << " ";
		query << "LIMIT 1;";
		mysqlpp::Result mRes = query.store();
		query.reset();
		
		if (mRes) {
			int mSize = mRes.size();
			
			if (mSize > 0) {
				mysqlpp::Row mRow = mRes.at(0);
				
				if (mRow["fleet_rtn_msg"]!="0")
					return false;
			}
		}
		return true;
	}
	
	void User::addCollectedWf(double res) {
		if (res>0) {
			My &my = My::instance();
			mysqlpp::Connection *con_ = my.get();
			
			mysqlpp::Query query = con_->query();
			query << "UPDATE ";
			query << "	users ";
			query << "SET ";
			query << "	user_res_from_tf=user_res_from_tf+'" << res << "' ";
			query << "WHERE ";
			query << "	user_id='" << this->userId << "' ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
		}
	}
	
	void User::addCollectedAsteroid(double res) {
		if (res>0) {
			My &my = My::instance();
			mysqlpp::Connection *con_ = my.get();
			
			mysqlpp::Query query = con_->query();
			query << "UPDATE ";
			query << "	users ";
			query << "SET ";
			query << "	user_res_from_asteroid=user_res_from_asteroid+'" << res << "' ";
			query << "WHERE ";
			query << "	user_id='" << this->userId << "' ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
		}
	}
	
	void User::addCollectedNebula(double res) {
		if (res>0) {
			My &my = My::instance();
			mysqlpp::Connection *con_ = my.get();
			
			mysqlpp::Query query = con_->query();
			query << "UPDATE ";
			query << "	users ";
			query << "SET ";
			query << "	user_res_from_nebula=user_res_from_nebula+'" << res << "' ";
			query << "WHERE ";
			query << "	user_id='" << this->userId << "' ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
		}
	}
	
	double User::getTechBonus(std::string tech) {
		if (!this->techsLoaded)
			this->loadTechs();
		return techs[tech]/10.0;
	}
	
	double User::getTechLevel(std::string tech) {
		if (!this->techsLoaded)
			this->loadTechs();
		return techs[tech];
	}
	
	void User::loadData() {
		if (!this->dataLoaded) {
			if (this->userId == 0) {
				this->userNick = "Unbekannter User";
				this->allianceId = 0;
				this->points = 0;
			}
			else {
				My &my = My::instance();
				mysqlpp::Connection *con_ = my.get();
				
				mysqlpp::Query query = con_->query();
				query << "SELECT ";
				query << "	user_nick, ";
				query << "	user_alliance_id, ";
				query << "	user_points ";
				query << "FROM ";
				query << " users ";
				query << "WHERE ";
				query << "	user_id='" << this->userId << "' ";
				query << "LIMIT 1;";
				mysqlpp::Result uRes = query.store();
				query.reset();
				
				if (uRes) {
					int uSize = uRes.size();
					
					if (uSize > 0) {
						mysqlpp::Row uRow = uRes.at(0);
						
						this->allianceId = (int)uRow["user_alliance_id"];
						this->userNick = std::string(uRow["user_nick"]);
						this->points = (double)uRow["user_points"];
						
						this->dataLoaded = true;
					}
					else {
						this->userNick = "Unbekannter User";
						this->allianceId = 0;
						this->points = 0;
					}
				}
			}
		}
	}
	
	void User::loadTechs() {
		if (!this->techsLoaded) {
			My &my = My::instance();
			mysqlpp::Connection *con_ = my.get();
			
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	techlist_tech_id, ";
			query << "	techlist_current_level, ";
			query << "	techlist_build_type ";
			query << "FROM ";
			query << " techlist ";
			query << "WHERE ";
			query << "	techlist_user_id='" << this->userId << "' ";
			query << "	AND techlist_current_level>'0';";
			mysqlpp::Result tRes = query.store();
			query.reset();
			
			if (tRes) {
				int tSize = tRes.size();
				this->techsLoaded = true;
				
				if (tSize > 0) {
					mysqlpp::Row tRow;
					DataHandler &DataHandler = DataHandler::instance();
					for (int i=0; i<tSize; i++) {
						tRow = tRes.at(i);
						TechData::TechData *data = DataHandler.getTechById((int)tRow["techlist_tech_id"]);
						techs[data->getName()] = (int)tRow["techlist_current_level"];
						if ((int)tRow["techlist_build_type"]==3) techAtWork = data->getName();
					}
				}
			}
		}
	}
	
	std::string User::stealTech(User* victim) {
		if (!this->techsLoaded)
			this->loadTechs();
		
		DataHandler &DataHandler = DataHandler::instance();
		std::map<std::string,int> avaiableTechs;
		
		std::map<std::string,int>::iterator it;
		for ( it=techs.begin() ; it != techs.end(); it++ ) {
			TechData::TechData *data = DataHandler.getTechByName((*it).first);
			if ((*it).second < victim->getTechLevel((*it).first) && data->getStealable() && (*it).first!=techAtWork)
				avaiableTechs[(*it).first] = victim->getTechLevel((*it).first);
		}
		
		if (avaiableTechs.size()) {
			int tech = rand() % avaiableTechs.size();
			for ( it=avaiableTechs.begin() ; it != avaiableTechs.end(); it++ ) {
				TechData::TechData *data = DataHandler.getTechByName((*it).first);
				if (!tech) {
					TechData::TechData *data = DataHandler.getTechByName((*it).first);
					My &my = My::instance();
					mysqlpp::Connection *con_ = my.get();
					
					mysqlpp::Query query = con_->query();
					query << "UPDATE ";
					query << "	techlist ";
					query << "SET ";
					query << "	techlist_current_level='" << (*it).second << "' ";
					query << "WHERE ";
					query << "	techlist_user_id='" << this->userId << "' ";
					query << "	AND techlist_tech_id='" << data->getId() << "' ";
					query << "LIMIT 1;";
					query.store();
					query.reset();
					
					return ((*it).first + " bis zum Level " + etoa::d2s((*it).second) + ".");
				}
				tech--;
			}
		}
		
		return "";
	}
	
	int User::getUserMain() {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	id  ";
		query << "FROM ";
		query << "	planets ";
		query << "WHERE ";
		query << "	planet_user_id='" << this->userId << "' ";
		query << "	AND planet_user_main='1' ";
		query << "LIMIT 1";
		mysqlpp::Result mainRes = query.store();
		query.reset();
		
		if (mainRes) {
			int mainSize = mainRes.size();
			
			if (mainSize > 0) {
				mysqlpp::Row mainRow = mainRes.at(0);
				return (int)mainRow["id"];
			}
		}
		
		return 0;
	}
	
	int User::getPlanetsCount() {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	COUNT(planet_user_id) as cnt ";
		query << "FROM ";
		query << "	planets ";
		query << "WHERE ";
		query << "	planet_user_id='" << this->userId << "';";
		mysqlpp::Result planetRes = query.store();
		query.reset();
		
		if (planetRes) {
			int planetSize = planetRes.size();
			
			if (planetSize > 0) {
				mysqlpp::Row planetRow = planetRes.at(0);
				return (int)planetRow["cnt"];
			}
		}
		
		return 0;
	}
	
	std::string User::getTechString() {
		if (!this->techsLoaded)
			this->loadTechs();
		std::string techString = "[b]TECHNOLOGIEN[/b]:\n";
		if (techs.size()) {
			techString += "[table]";
			std::map<std::string,int>::iterator it;
			for ( it=techs.begin() ; it != techs.end(); it++ )
				techString += "[tr][td]" + (*it).first + "[/td][td]" + etoa::d2s((*it).second) + "[/td][/tr]";
			
			techString += "[/table]";
		}
		else
			techString += "[i]Nichts vorhanden[/i]\n";
		
		return techString;
	}
	
