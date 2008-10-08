#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "../config/ConfigHandler.h"
#include "aPointsHandler.h"

namespace aPoints
{
	void aPointsHandler::update()
	{
		std::time_t time = std::time(0);
		mysqlpp::Query query = con_->query();
		Config &config = Config::instance();
		
		query << "SELECT ";
		query << "	alliance_buildlist_alliance_id, ";
		query << "	alliance_buildlist_current_level ";
		query << "FROM ";
		query << "	alliance_buildlist ";
		query << "WHERE ";
		query << "	alliance_buildlist_building_id='3';";
		mysqlpp::Result res = query.store();		
		query.reset();
		
		if (res)  {
			int resSize = res.size();
			
			if (resSize>0) {
				mysqlpp::Row arr;
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
					arr = res.at(i);
		
					// Berechnet Schiffspunkte die addiert werden
					int shipPointsAdd = (2 + (int)arr["alliance_buildlist_current_level"]) * (int)config.nget("alliance_shippoints_per_hour", 0);
		
					// Speichern
					query << "UPDATE ";
					query << "	users ";
					query << "SET ";
					query << "user_alliace_shippoints=user_alliace_shippoints + '" << shipPointsAdd << "' ";
					query << "WHERE ";
					query << "	user_alliance_id='" << arr["alliance_buildlist_alliance_id"] << "';";
					query.store();
					query.reset();
				}
			}
		}
	}
}
