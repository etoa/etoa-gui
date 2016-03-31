
#include "User.h"
#include "../config/ConfigHandler.h"

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
	
	int User::getElorating() {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		this->elorating = 0;
		
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	elorating "
			<< "FROM "
			<< "	user_ratings "
			<< "WHERE "
			<< "	id='" << this->userId << "' "
			<< "LIMIT 1;";
		RESULT_TYPE eloRes = query.store();
		query.reset();
		
		if (eloRes) {
			int eloSize = eloRes.size();
			
			if (eloSize > 0) {
				mysqlpp::Row eloRow = eloRes.at(0);
				this->elorating = (int)eloRow["elorating"];
			}
		}
		
		if (this->elorating==0) {
			Config &config = Config::instance();
			this->elorating = (int)config.nget("elorating",0);
		}
		return this->elorating;
	}
	
	void User::addElorating(int newRating) {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "UPDATE "
			<< "	user_ratings "
			<< "SET "
			<< "	elorating='" << newRating << "' "
			<< "WHERE "
			<< "	id='" << this->userId << "' "
			<< "LIMIT 1;";
		query.store();
		query.reset();
	}

	int User::getSpyattackCount() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->spyattackCount;
	}
	
	SpecialistData* User::getSpecialist() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->specialist;
	}
	
	void User::setDiscovered(short absX, short absY) {
	    this->setDiscovered(absX, absY, 1);
    }    
        
    void User::setDiscovered(short absX, short absY, short radius) {
		
		Config &config = Config::instance();
		int sxNum = (int)config.nget("num_of_sectors",1);
		int cxNum = (int)config.nget("num_of_cells",1);
		int syNum = (int)config.nget("num_of_sectors",2);
		int cyNum = (int)config.nget("num_of_cells",2);
		
		// the mask
		char mask[10000] = "";
		
		
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	discoverymask "
			<< "FROM "
			<< "	users "
			<< "WHERE "
			<< "	user_id='" << this->userId << "' "
			<< "LIMIT 1;";
		RESULT_TYPE maskRes = query.store();
		query.reset();
		
		if (maskRes) {
			int maskSize = maskRes.size();
			
			if (maskSize > 0) {
				mysqlpp::Row maskRow = maskRes.at(0);
				strcpy( mask, maskRow["discoverymask"]);
			}
		}
						
		for (int x = absX - radius; x <= absX + radius; x++) {
			for (int y = absY - radius; y <= absY + radius; y++) {
				int pos = x + (cyNum * syNum) * (y - 1) - 1;
				if (pos >= 0 && pos <= sxNum * syNum * cxNum * cyNum) {
					mask[pos] = '1';
				}
			}
		}	
						
		// Update the mask
		query << "UPDATE "
			<< "	users "
			<< "SET "
			<< " discoverymask='" << mask << "' "
			<< "WHERE "
			<< "	user_id='" << this->userId << "' "
			<< "LIMIT 1;";
		query.store();
		query.reset();
	}

	void User::setLastInvasion() {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "UPDATE "
			<< "	users "
			<< "SET "
			<< "	lastinvasion='" << time(0) << "' "
			<< "WHERE "
			<< " user_id='" << this->userId << "' "
			<< "LIMIT 1;";
		query.store();
		query.reset();
	}
	
	bool User::getPropertiesReturnMsg() {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	fleet_rtn_msg "
			<< "FROM "
			<< "	user_properties "
			<< "WHERE "
			<< "	id=" << this->userId << " "
			<< "LIMIT 1;";
		RESULT_TYPE mRes = query.store();
		query.reset();
		
		if (mRes) {
			int mSize = mRes.size();
			
			if (mSize > 0) {
				mysqlpp::Row mRow = mRes.at(0);
				
				if (mRow["fleet_rtn_msg"]=="0")
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
			query << "UPDATE "
				<< "	users "
				<< "SET "
				<< "	user_res_from_tf=user_res_from_tf+'" << res << "' "
				<< "WHERE "
				<< "	user_id='" << this->userId << "' "
				<< "LIMIT 1;";
			query.store();
			query.reset();
		}
	}
	
	void User::addCollectedAsteroid(double res) {
		if (res>0) {
			My &my = My::instance();
			mysqlpp::Connection *con_ = my.get();
			
			mysqlpp::Query query = con_->query();
			query << "UPDATE "
				<< "	users "
				<< "SET "
				<< "	user_res_from_asteroid=user_res_from_asteroid+'" << res << "' "
				<< "WHERE "
				<< "	user_id='" << this->userId << "' "
				<< "LIMIT 1;";
			query.store();
			query.reset();
		}
	}
	
	void User::addCollectedNebula(double res) {
		if (res>0) {
			My &my = My::instance();
			mysqlpp::Connection *con_ = my.get();
			
			mysqlpp::Query query = con_->query();
			query << "UPDATE "
				<< "	users "
				<< "SET "
				<< "	user_res_from_nebula=user_res_from_nebula+'" << res << "' "
				<< "WHERE "
				<< "	user_id='" << this->userId << "' "
				<< "LIMIT 1;";
			query.store();
			query.reset();
		}
	}
	
	void User::addRaidedRes(double res) {
		if (res>0) {
			My &my = My::instance();
			mysqlpp::Connection *con_ = my.get();
			
			mysqlpp::Query query = con_->query();
			query << "UPDATE "
				<< "	users "
				<< "SET "
				<< "	user_res_from_raid=user_res_from_raid+'" << res << "' "
				<< "WHERE "
				<< "	user_id='" << this->userId << "' "
				<< "LIMIT 1;";
			query.store();
			query.reset();
		}
	}
	
	double User::getTechBonus(unsigned int tech) {
		if (!this->techsLoaded)
			this->loadTechs();
		return techs[tech]/10.0;
	}
	
	unsigned int User::getTechLevel(unsigned int tech) {
		if (!this->techsLoaded)
			this->loadTechs();
		return techs[tech];
	}

	unsigned int User::getTechLevel(std::string tech) {
		unsigned int techlvl= 0;

		My &my= My::instance();
		mysqlpp::Connection *con_ = my.get();

		mysqlpp::Query query= con_->query();
		query << "SELECT "
			<< "	tech_id "
			<< "FROM "
			<< "	technologies "
			<< "WHERE "
			<< "	tech_name = " << mysqlpp::quote << tech << " "
			<< "LIMIT 1;";
		RESULT_TYPE res = query.store();
		query.reset();

		if( res && res.size() > 0 ) {
			unsigned int techid= res.at(0)[res.field_num("tech_id")];
			techlvl= this->getTechLevel(techid);
		}
		return techlvl;
	}
	
	void User::loadData() {
		if (!this->dataLoaded) {
			if (this->userId == 0) {
				this->userNick = "Unbekannter User";
				this->allianceId = 0;
				this->points = 0;
				this->inactiv = true;
			}
			else {
				My &my = My::instance();
				mysqlpp::Connection *con_ = my.get();
				
				mysqlpp::Query query = con_->query();
				query << "SELECT "
					<< "	user_nick, "
					<< "	user_alliance_id, "
					<< "	user_points, "
					<< "	user_specialist_id, "
					<< "	user_specialist_time, "
					<< "	spyattack_counter, "
					<< "	user_hmode_from, "
					<< "	user_logouttime "
					<< "FROM "
					<< " users "
					<< "WHERE "
					<< "	user_id='" << this->userId << "' "
					<< "LIMIT 1;";
				RESULT_TYPE uRes = query.store();
				query.reset();
				
				if (uRes) {
					int uSize = uRes.size();
					
					if (uSize > 0) {
						mysqlpp::Row uRow = uRes.at(0);
						this->allianceId = (int)uRow["user_alliance_id"];
						this->userNick = std::string(uRow["user_nick"]);
						this->points = (double)uRow["user_points"];
						this->spyattackCount = (int)uRow["spyattack_counter"];
						this->inactiv = (int)uRow["user_hmode_from"] == 0 && ((int)uRow["user_logouttime"] < (time(NULL) - Config::instance().idget("USER_INACTIVE_DAYS") * 86400)) ? true : false;
						
						DataHandler &DataHandler = DataHandler::instance();
						if ((int)uRow["user_specialist_id"]>0 && (int)uRow["user_specialist_time"]>time(0)) {
							this->specialist = DataHandler.getSpecialistById((int)uRow["user_specialist_id"]);
						}
						else {
							this->specialist = DataHandler.getSpecialistById(0);
						}
						
						this->dataLoaded = true;
					}
					else {
						this->userNick = "Unbekannter User";
						this->allianceId = 0;
						this->points = 0;
						this->inactiv = true;
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
			query << "SELECT "
				<< "	techlist_tech_id, "
				<< "	techlist_current_level, "
				<< "	techlist_build_type "
				<< "FROM "
				<< " techlist "
				<< "WHERE "
				<< "	techlist_user_id='" << this->userId << "' "
				<< "	AND techlist_current_level>'0' ";
			RESULT_TYPE tRes = query.store();
			query.reset();
			
			if (tRes) {
				int tSize = tRes.size();
				this->techsLoaded = true;
				
				if (tSize > 0) {
					mysqlpp::Row tRow;
					for (int i=0; i<tSize; i++) {
						tRow = tRes.at(i);
						this->techs[(int)tRow["techlist_tech_id"]] = (int)tRow["techlist_current_level"];
						if (((int)tRow["techlist_build_type"]==3)&&((int)tRow["techlist_tech_id"]!=Config::instance().idget("GEN_TECH_ID"))) 
							techAtWork = tRow["techlist_tech_id"];
					}
				}
			}
		}
	}
	
	std::string User::stealTech(User* victim) {
		if (!this->techsLoaded)
			this->loadTechs();
		
		DataHandler &DataHandler = DataHandler::instance();
		std::map<int,int> avaiableTechs;
		
		std::map<int,int>::iterator it;
		for ( it=this->techs.begin() ; it != this->techs.end(); it++ ) {
			if ((unsigned int)(*it).second && (unsigned int)(*it).second < victim->getTechLevel((*it).first)) {
				if ((unsigned int)(*it).first != techAtWork) {
					TechData *data = DataHandler.getTechById((*it).first);
					if (data->getStealable())
						avaiableTechs[(*it).first] = victim->getTechLevel((*it).first);
				}
			}
		}
		
		if (avaiableTechs.size()) {
			int tech = rand() % avaiableTechs.size();
			for ( it=avaiableTechs.begin() ; it != avaiableTechs.end(); it++ ) {
				if (!tech) {
					My &my = My::instance();
					mysqlpp::Connection *con_ = my.get();
					
					mysqlpp::Query query = con_->query();
					query << "UPDATE "
						<< "	techlist "
						<< "SET "
						<< "	techlist_current_level='" << (*it).second << "' "
						<< "WHERE "
						<< "	techlist_user_id='" << this->userId << "' "
						<< "	AND techlist_tech_id='" << (*it).first << "' "
						<< "LIMIT 1;";
					query.store();
					query.reset();
					
					return (etoa::d2s((*it).first) + ":" + etoa::d2s((*it).second));
				}
				tech--;
			}
		}
		
		return "";
	}

	void User::addSpyattackCount() {
		if (!this->dataLoaded)
		    this->loadData();
	    
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();		
		query << "UPDATE "
			<< "	users "
			<< "SET "
			<< "	spyattack_counter='" << ++this->spyattackCount << "' "
			<< "WHERE "
			<< "	user_id='" << this->userId << "' "
			<< "LIMIT 1;";
		query.store();
		query.reset();
	}
	
	int User::getUserMain() {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	id  "
			<< "FROM "
			<< "	planets "
			<< "WHERE "
			<< "	planet_user_id='" << this->userId << "' "
			<< "	AND planet_user_main='1' "
			<< "LIMIT 1";
		RESULT_TYPE mainRes = query.store();
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
		query << "SELECT "
			<< "	COUNT(planet_user_id) as cnt "
			<< "FROM "
			<< "	planets "
			<< "WHERE "
			<< "	planet_user_id='" << this->userId << "';";
		RESULT_TYPE planetRes = query.store();
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
		std::string techString = "";
		if (techs.size()) {
			std::map<int,int>::iterator it;
			for ( it=techs.begin() ; it != techs.end(); it++ )
				techString += etoa::d2s((*it).first) + ":" + etoa::d2s((*it).second) + ",";
		}
		else
			techString += "0";
		
		return techString;
	}

	bool User::isInactiv() {
		return this->inactiv;
	}
	
	bool User::isAtWarWith(int opponentAllianceId)
	{
            if (this->getAllianceId() != 0 && opponentAllianceId != 0)
            {
                My &my = My::instance();
                mysqlpp::Connection *con_ = my.get();
                mysqlpp::Query query = con_->query();
                
                query << "SELECT ";
                query << "  alliance_bnd_id ";
                query << "FROM ";
                query << "  alliance_bnd ";
                query << "WHERE ";
                query << "  (alliance_bnd_alliance_id1='" << this->getAllianceId() << "' ";
                query << "  AND alliance_bnd_alliance_id2='" << opponentAllianceId << "') ";
                query << "OR ";
                query << "  (alliance_bnd_alliance_id1='" << opponentAllianceId << "' ";
                query << "  AND alliance_bnd_alliance_id2='" << this->getAllianceId() << "') ";
                query << "  AND alliance_bnd_level='3';";
                
                RESULT_TYPE warCheckRes = query.store();
                query.reset();
                
                if (warCheckRes && warCheckRes.size() > 0)
                {
                    return true;
                }
            }
            return false;
        }
