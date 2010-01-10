
#include "aPointsHandler.h"

namespace aPoints
{
	void aPointsHandler::update()
	{
		
		//std::time_t time = std::time(0);  Unused
		mysqlpp::Query query = con_->query();
		Config &config = Config::instance();
		
		query << "SELECT "
			<< "	alliance_buildlist_alliance_id, "
			<< "	alliance_buildlist_current_level, "
			<< "	alliance_res_metal, "
			<< "	alliance_res_crystal, "
			<< "	alliance_res_plastic, "
			<< "	alliance_res_fuel, "
			<< "	alliance_res_food "
			<< "FROM "
			<< "	alliance_buildlist "
			<< "INNER JOIN "
			<< "	alliances "
			<< "ON "
			<< "	alliance_id=alliance_buildlist_alliance_id "
			<< "	AND alliance_buildlist_building_id='3';";
		RESULT_TYPE res = query.store();
		query.reset();
		
		if (res) {
			unsigned int resSize = res.size();
			
			if (resSize>0) {
				mysqlpp::Row arr;
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
					arr = res.at(i);
					
					if (!((double)arr["alliance_res_metal"]<0 
						  || (double)arr["alliance_res_crystal"]<0 
						  || (double)arr["alliance_res_plastic"]<0 
						  || (double)arr["alliance_res_fuel"]<0 
						  || (double)arr["alliance_res_food"]<0)) {
						
						// Berechnet Schiffspunkte die addiert werden
						int shipPointsAdd = (int)arr["alliance_buildlist_current_level"] * (int)config.nget("alliance_shippoints_per_hour", 0);
						
						// Speichern
						query << "UPDATE "
							<< "	users "
							<< "SET "
							<< "user_alliace_shippoints=user_alliace_shippoints + '" << shipPointsAdd << "' "
							<< "WHERE "
							<< "	user_alliance_id='" << arr["alliance_buildlist_alliance_id"] << "';";
						query.store();
						query.reset();
					}
				}
			}
		}
	}
}
