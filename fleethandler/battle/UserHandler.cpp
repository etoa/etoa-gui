#include <vector>
#include <math.h>
#include "../config/ConfigHandler.h"
#include "../MysqlHandler.h"
#include "UserHandler.h"

	void UserHandler::getValues()
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		Config &config = Config::instance();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	user_nick, ";
		query << "	user_alliance_id ";
		query << "FROM ";
		query << "	users ";
		query << "WHERE ";
		query << "	user_id='" << this->userId << "';";
		mysqlpp::Result userRes = query.store();
		query.reset();
		
		if (userRes) {
			int userSize = userRes.size();
			
			if (userSize > 0) {
				mysqlpp::Row userRow = userRes.at(0);
				
				if (std::string(userRow["user_nick"])!="") {
					this->userNick = std::string(userRow["user_nick"]);
				}
				else {
					this->userNick = "Unbekannter User";
				}
				this->allianceId = (int)userRow["user_alliance_id"];
			}
		}
		
		
		query << "SELECT ";
		query << "	techlist_tech_id, ";
		query << "	techlist_current_level ";
		query << "FROM ";
		query << "	techlist ";
		query << "WHERE ";
		query << "	techlist_user_id='" << this->userId << "' ";
		query << "	AND ";
		query << "	(";
		query << "		techlist_tech_id='" << config.idget("STRUCTURE_TECH_ID") << "' ";
		query << "		OR techlist_tech_id='" << config.idget("SHIELD_TECH_ID") <<  "' ";
		query << "		OR techlist_tech_id='" << config.idget("WEAPON_TECH_ID") << "' ";
		query << "		OR techlist_tech_id='" << config.idget("REGENA_TECH_ID") << "' ";
		query << "	);";
		mysqlpp::Result techRes = query.store();
		query.reset();
		
		if (techRes) {
			int techSize = techRes.size();
			
			if (techSize > 0) {
				mysqlpp::Row techRow;
				
				for (mysqlpp::Row::size_type i = 0; i<techSize; i++)  {
					techRow = techRes.at(i);
					
					if ((int)techRow["techlist_tech_id"]==config.idget("SHIELD_TECH_ID"))
						this->shieldTech += ((float)techRow["techlist_current_level"]/10);

					if ((int)techRow["techlist_tech_id"]==config.idget("STRUCTURE_TECH_ID"))
						this->structureTech += ((float)techRow["techlist_current_level"]/10);

					if ((int)techRow["techlist_tech_id"]==config.idget("WEAPON_TECH_ID"))
						this->weaponTech += ((float)techRow["techlist_current_level"]/10);

					if ((int)techRow["techlist_tech_id"]==config.idget("REGENA_TECH_ID"))
						this->healTech += ((float)techRow["techlist_current_level"]/10);
				}
			}
		}
	}
