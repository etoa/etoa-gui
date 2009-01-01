
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
	
	void User::loadData() {
		if (!this->dataLoaded) {
			if (this->userId == 0) {
				this->userNick = "Unbekannter User";
				this->allianceId = 0;
			}
			else {
				My &my = My::instance();
				mysqlpp::Connection *con_ = my.get();
				
				mysqlpp::Query query = con_->query();
				query << "SELECT ";
				query << "	user_nick, ";
				query << "	user_alliance_id ";
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
						
						this->dataLoaded = true;
					}
					else {
						this->userNick = "Unbekannter User";
						this->allianceId = 0;
					}
				}
			}
		}
	}